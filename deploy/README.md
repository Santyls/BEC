# Despliegue BEC — 2 servidores (público/privado)

Adaptado del demo que compartió el profe (`Proxy y Balanceo/demo - Copy`) a los
4 servicios reales de BEC. Sin dominio: certificado autofirmado, un puerto
HTTPS distinto por app (evita tener que reescribir rutas internas de
Laravel/Flask). Solo `BEC_API` se replica (3 instancias, balanceadas de
verdad); `BEC_PAL` y `BEC_PRF` van en una sola instancia cada uno porque usan
sesión de servidor.

## 0. Antes de nada

En DigitalOcean, crea los 2 droplets **en la misma región** (comparten VPC
automáticamente). El droplet **privado se crea sin Public IPv4** (lo
desactivas en el formulario de creación) — no solo queda firewalleado, de
plano no tiene forma de que internet le llegue directo. Anota:
- IP **pública** del droplet público (la vas a necesitar tú, y en `bec_movil/src/config/env.js`).
- IP **privada** de AMBOS droplets (te las da DO al crearlos, sección "Networking" del droplet ya creado).

Como el privado no tiene IP pública, para entrar a él tienes dos opciones:
- **Botón "Console" en el dashboard de DigitalOcean** (consola web, no necesita IP) — sirve para los comandos, pero es incómoda para copiar archivos grandes.
- **SSH "saltando" por el público** (mejor para el `scp` de las carpetas del proyecto), desde tu máquina:
  ```bash
  ssh -J root@24.144.123.16 root@10.116.0.3
  scp -o ProxyJump=root@24.144.123.16 -r deploy/ BEC_API/ BEC_PAL/ BEC_PRF/ root@10.116.0.3:/root/
  ```

En ambos droplets: instala Docker + Docker Compose plugin (`curl -fsSL https://get.docker.com | sh`).

Copia la carpeta `deploy/` a los dos droplets, y además `BEC_API/`, `BEC_PAL/`,
`BEC_PRF/` al **privado** (son los que se construyen ahí — el público solo
necesita `deploy/`).

**Importante sobre BEC_PAL y BEC_PRF**: sus Dockerfiles NO copian el código
dentro de la imagen (dependen del bind-mount, igual que en tu máquina local) —
y BEC_PAL ni siquiera corre `composer install`. Por eso lo que copies al
droplet debe ser la **carpeta local tal cual está** (con `vendor/` ya
instalado y su `.env` con `APP_KEY`), **no** un `git clone` limpio — `vendor/`
normalmente está en `.gitignore` así que un clone fresco llegaría vacío y el
contenedor no arrancaría. Usa `scp -r` o `rsync` desde tu carpeta local, no git.

## 1. Certificado autofirmado

Solo hace falta en el droplet **público**. Corre esto dentro de `deploy/haproxy/certs/`
(reemplaza `24.144.123.16` por la IP pública real):

```bash
openssl req -x509 -nodes -newkey rsa:2048 -days 365 \
  -keyout server.key -out server.crt \
  -subj "/CN=24.144.123.16" \
  -addext "subjectAltName=IP:24.144.123.16"

cat server.crt server.key > server.pem
```

## 2. Variables de entorno

En **ambos** droplets, dentro de `deploy/`:
```bash
cp .env.example .env
nano .env   # rellena POSTGRES_PASSWORD, SECRET_KEY, FLASK_SECRET_KEY, BEC_API_PUBLIC_URL,
            # GRAFANA_ADMIN_PASSWORD, STATS_PASSWORD (puedes reusar los valores
            # de tu .env local para SECRET_KEY/FLASK_SECRET_KEY si quieres)
```

Reemplaza también `10.116.0.3` por la IP privada real en:
- `deploy/haproxy/haproxy.cfg` (3 apariciones: pal_back, prf_back, api_back)
- `deploy/grafana/provisioning/datasources/prometheus.yml`

## 3. Levantar los servicios

**Droplet privado:**
```bash
cd deploy
docker compose -f compose.private.yaml up -d --build
```

**Droplet público** (después de que el privado ya esté arriba):
```bash
cd deploy
docker compose -f compose.public.yaml up -d
```

## 4. Firewall (ufw) — el punto que va a revisar el profe

El droplet privado ya es inalcanzable desde internet por no tener IP pública
(eso lo cierra a nivel de red, no solo de firewall) — aun así configura ufw en
los dos como segunda capa:

**Droplet público** — solo lo que de verdad necesita estar abierto al mundo:
```bash
ufw default deny incoming
ufw allow 22/tcp        # SSH — considera restringirlo a tu IP si puedes
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow 8444/tcp
ufw allow 8446/tcp
ufw allow 8404/tcp       # stats — ya tiene auth, pero si quieres ciérralo a tu IP:
                          #   ufw allow from TU_IP to any port 8404
ufw enable
```

> **Cuidado: ufw NO cierra un puerto publicado por Docker.** Docker mete sus
> propias reglas DNAT desde `0.0.0.0/0` que se saltan la cadena de ufw, así que
> un `ufw deny 8405` es puramente cosmético — el puerto sigue abierto al mundo.
> La única forma real de no exponer un puerto es no publicarlo en la interfaz
> pública: en `compose.public.yaml` el puerto de métricas va como
> `"10.116.0.2:8405:8405"` (atado a la IP de la VPC) en vez de `"8405:8405"`.
> Comprobación: `iptables -t nat -L DOCKER -n | grep <puerto>`.

**Droplet privado**:
```bash
ufw default deny incoming
ufw allow 22/tcp
ufw allow from 10.116.0.2    # IP PRIVADA (VPC) del droplet público
ufw enable
```
(Usa la IP **privada** del droplet público aquí, no la pública — así solo él,
por la VPC, puede llegar a los puertos 5001/5002/8001-8003/9090.)

## 5. Verificar

- `https://24.144.123.16` → BEC_PAL (admin)
- `https://24.144.123.16:8444` → BEC_PRF (recepción)
- `https://24.144.123.16:8446/health` → BEC_API
- `https://24.144.123.16/grafana/` → Grafana (usuario `admin`, la contraseña que pusiste en `.env`) — el datasource de Prometheus ya debería estar cargado solo.
- `https://24.144.123.16:8404/stats` → panel de HAProxy (usuario `admin`, `STATS_PASSWORD`)
- Desde OTRA máquina (no los droplets): `nmap -Pn 24.144.123.16` → deberías ver SOLO 80/443/8444/8446/8404/8405 (+22) abiertos, nada más. Y `nmap -Pn 10.116.0.3` desde afuera de la VPC no debería ni siquiera responder.

## 6. App móvil

En `bec_movil/src/config/env.js`:
```js
export const API_BASE_URL = 'https://24-144-123-16.nip.io:8446';
```

Va por el hostname de `nip.io` y no por la IP a propósito: React Native rechaza
los certificados autofirmados, así que hace falta uno real de Let's Encrypt, y
Let's Encrypt no emite certificados para direcciones IP. `nip.io` resuelve
`24-144-123-16.nip.io` → `24.144.123.16` sin tener que comprar un dominio, lo
que permitió sacar el certificado con `certbot --standalone`.

## 7. Endurecimiento (hecho el 2026-08-05)

**Swap en los dos droplets.** Con 1 GB de RAM y sin swap, el droplet privado se
quedó sin memoria dos veces (el seed de códigos postales y un rebuild de la
API). La swap no sustituye a más RAM, pero evita que el kernel mate procesos:

```bash
fallocate -l 2G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab
sysctl -w vm.swappiness=10 && echo 'vm.swappiness=10' >> /etc/sysctl.conf
```

`swappiness=10` la deja como red de seguridad ante un pico y no como memoria de
uso diario (que haría todo más lento).

**Failover transparente en HAProxy.** Con la configuración inicial
(`check inter 30s`, `fall 3` por defecto), al detener una réplica de la API
HAProxy le seguía mandando tráfico hasta 90 segundos: medido, 7 de cada 20
peticiones fallaban. Con `retries 3` + `option redispatch` en `defaults` y
`check inter 2s fall 2 rise 2` en `api_back`, la misma prueba da 30/30 correctas
y la réplica caída se detecta en ~4 s.

Los health checks de `pal_back`/`prf_back` se quedan en `inter 30s` a propósito:
`php artisan serve` es de un solo hilo y un chequeo cada 2 s compite con las
peticiones reales de login.
