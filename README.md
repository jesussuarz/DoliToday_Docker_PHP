# DoliToday PHP Proxy (Docker + cURL CLI)

## 📌 Descripción

Este proyecto permite consumir de forma confiable el endpoint:

```
https://dolitoday.com/api/rate
```

desde aplicaciones PHP, evitando bloqueos provocados por Cloudflare.

A diferencia de implementaciones anteriores (como DolarToday clásico), este enfoque utiliza **Docker + PHP + cURL CLI** para garantizar estabilidad en entornos donde las peticiones HTTP tradicionales son bloqueadas.

---

## 🚨 Problema

El endpoint de DoliToday está protegido por **Cloudflare**, lo que implica:

* Bloqueo de requests automatizados (PHP cURL, bots, etc.)
* Respuestas `403 Forbidden` o HTML en lugar de JSON
* Detección avanzada basada en:

  * TLS fingerprint
  * comportamiento de cliente
  * headers y cookies ([ZenRows][1])

Esto provoca errores como:

```
Error al decodificar JSON
```

porque la respuesta no es JSON real.

---

## 🧠 Observación clave

Se detectó que:

```bash
curl https://dolitoday.com/api/rate
```

✔ Funciona desde consola
❌ No funciona desde PHP (`curl_exec`)

Esto ocurre porque:

* `curl` CLI usa un stack de red completo (TLS, HTTP/2, etc.)
* PHP usa libcurl con un fingerprint diferente (detectado como bot)

---

## 💡 Solución implementada

Se construyó un **proxy HTTP en Docker** que:

1. Ejecuta `curl` desde CLI (no desde PHP)
2. Obtiene el JSON real
3. Lo expone como API interna

---

## ⚙️ Arquitectura

```
[ Cliente / PHP / App ]
           ↓
   http://localhost:8080
           ↓
   Docker (PHP + Apache)
           ↓
   curl CLI (real)
           ↓
 https://dolitoday.com/api/rate
```

---

## 🚀 Ventajas de este enfoque

### ✔ Evita bloqueos de Cloudflare

Se usa el mismo mecanismo que funciona en consola.

### ✔ Independencia del hosting

No depende de:

* configuración de PHP
* extensiones
* restricciones del servidor

### ✔ Portabilidad

Corre igual en:

* VPS
* WSL2
* Linux
* servidores productivos

### ✔ Escalable

Se puede convertir fácilmente en:

* microservicio
* API interna
* servicio cacheado

---

## 📦 Uso

### 1. Clonar el repositorio
```bash
git clone https://github.com/jesussuarz/DoliToday_Docker_PHP.git
cd DoliToday_Docker_PHP
```

### 2. Levantar el servicio

```bash
docker compose up -d --build
```

---

### 3. Consumir endpoint

```bash
curl http://localhost:8080
```

o desde PHP:

```php
<?php
$response = file_get_contents("http://localhost:8080");
$data = json_decode($response, true);

print_r($data);
```

---

## 📊 Ejemplo de respuesta

```json
{
  "pair": "USDT/VES",
  "rate": 661.26,
  "binance_rate": 651.49,
  "bcv_rate": 474.0598,
  "eur_rate": 767.06
}
```

---

## ⚠️ Buenas prácticas

* No hacer requests excesivos (Cloudflare puede bloquear)
* Implementar cache local (recomendado)
* Evitar uso directo del endpoint externo desde producción

Las APIs suelen aplicar límites y protecciones para evitar abuso y sobrecarga ([DEV Community][2]).

---

## 🔥 Mejoras recomendadas

* Cache (Redis / archivo)
* Fallback a otras APIs
* Rate limiting interno
* Logs de errores

---

## 🧠 Conclusión

Este proyecto no es solo un script PHP, sino una **adaptación a restricciones modernas de seguridad web**.

Dado que servicios como Cloudflare bloquean tráfico automatizado, la solución consiste en:

👉 usar herramientas que simulen comportamiento real (cURL CLI)
👉 aislarlas en un servicio (Docker)
👉 exponer un endpoint limpio para consumo interno

---

## ⚠️ Disclaimer

Este proyecto es solo para fines educativos y de integración.
No está afiliado a DoliToday ni a Cloudflare.

Usar con responsabilidad.

[1]: https://www.zenrows.com/blog/curl-bypass-cloudflare?utm_source=chatgpt.com "4 Methods to Bypass Cloudflare with cURL - ZenRows"
[2]: https://dev.to/mehmetakar/api-rate-limit-exceeded-how-to-fix-5-best-practices-4n13?utm_source=chatgpt.com "\"API Rate Limit Exceeded\" How to Fix: 5 Best Practices"
