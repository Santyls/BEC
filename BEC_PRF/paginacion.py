from urllib.parse import urlencode

from flask import request


class Paginador:
    """Pagina en memoria una lista ya filtrada/buscada (los datos vienen de BEC_API
    como listas de dicts, no hay ORM/ query que pagine por sí solo). Conserva los
    demás parámetros de la URL (?q=, ?categoria_id=, etc.), análogo al Paginador.php
    de BEC_PAL.
    """

    def __init__(self, items: list, por_pagina: int = 15):
        self.total = len(items)
        self.por_pagina = por_pagina
        self.ultima_pagina = max(1, -(-self.total // por_pagina))

        try:
            pagina = int(request.args.get("page", 1))
        except (TypeError, ValueError):
            pagina = 1
        self.pagina_actual = min(max(1, pagina), self.ultima_pagina)

        inicio = (self.pagina_actual - 1) * por_pagina
        self.items = items[inicio : inicio + por_pagina]

    @property
    def tiene_paginas(self) -> bool:
        return self.ultima_pagina > 1

    @property
    def primer_item(self) -> int:
        return 0 if self.total == 0 else (self.pagina_actual - 1) * self.por_pagina + 1

    @property
    def ultimo_item(self) -> int:
        return min(self.total, self.pagina_actual * self.por_pagina)

    def url_pagina(self, pagina: int) -> str:
        args = request.args.to_dict(flat=True)
        args["page"] = pagina
        return f"{request.path}?{urlencode(args)}"

    def rango_paginas(self, ventana: int = 2) -> range:
        inicio = max(1, self.pagina_actual - ventana)
        fin = min(self.ultima_pagina, self.pagina_actual + ventana)
        return range(inicio, fin + 1)

    def __iter__(self):
        return iter(self.items)

    def __len__(self):
        return len(self.items)
