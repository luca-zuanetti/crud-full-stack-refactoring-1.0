/**
*    File        : frontend/js/api/apiFactory.js
*    Project     : CRUD PHP
*    Author      : Tecnologías Informáticas B - Facultad de Ingeniería - UNMdP
*    License     : http://www.gnu.org/licenses/gpl.txt  GNU GPL 3.0
*    Date        : Mayo 2025
*    Status      : Prototype
*    Iteration   : 3.0 ( prototype )
*/

export function createAPI(moduleName, config = {}) 
{
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;

    async function sendJSON(method, data) 
        {
        const res = await fetch(API_URL,
        {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        // --- INICIO DE LA MODIFICACIÓN (PASO 3 CORREGIDO) ---
        if (!res.ok) 
        {
            // Si la respuesta no es OK (ej: 409, 500), 
            // intentamos leer el cuerpo del error (nuestro JSON del backend)
            const errorData = await res.json();
            
            // Lanzamos un error usando el mensaje específico del backend
            // Si por alguna razón no hay mensaje, usamos uno genérico
            throw new Error(errorData.error || `Error en ${method}`);
        }
        // --- FIN DE LA MODIFICACIÓN ---

        return await res.json();
        }
    return {
        async fetchAll()
        {
            const res = await fetch(API_URL);

            // --- INICIO DE LA MODIFICACIÓN (PASO 3 CORREGIDO) ---
            if (!res.ok) 
            {
                // Hacemos lo mismo para fetchAll por consistencia
                const errorData = await res.json();
                throw new Error(errorData.error || "No se pudieron obtener los datos");
            }
            // --- FIN DE LA MODIFICACIÓN ---

            return await res.json();
        },        
        //2.0
        async fetchPaginated(page = 1, limit = 10)
        {
            const url = `${API_URL}&page=${page}&limit=${limit}`;
            const res = await fetch(url);
            if (!res.ok)
                throw new Error("Error al obtener datos paginados");
            return await res.json();
        },
        async create(data)
        {
            return await sendJSON('POST', data);
        },
        async update(data)
        {
            return await sendJSON('PUT', data);
        },
        async remove(id)
        {
            return await sendJSON('DELETE', { id });
        }
    };
}
