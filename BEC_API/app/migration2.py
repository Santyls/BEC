from data.database import engine
from sqlalchemy import text
import os

with engine.begin() as conn:
    conn.execute(text('ALTER TABLE campanas DROP COLUMN IF EXISTS id_Estado_campana CASCADE'))
    conn.execute(text('ALTER TABLE campanas ADD COLUMN id_Estado_campana INTEGER NOT NULL DEFAULT 1'))
    conn.execute(text('ALTER TABLE campanas ADD CONSTRAINT fk_estado_campana FOREIGN KEY (id_Estado_campana) REFERENCES estados_campanas("Id_Estado_Campana")'))

if os.path.exists('/app/app/migration.py'):
    os.remove('/app/app/migration.py')
print("Migrated campanas field to INTEGER FK")
