# Documentación del proyecto

## ¿Qué hace?

Este proyecto transforma texto escrito en español aplicando una serie de reglas
ortográficas. No es un traductor de significado: toma el texto y modifica letras
y terminaciones según las reglas implementadas en PHP.

La aplicación tiene dos partes:

- `transcriptor.js`: conecta los botones de la página con el servidor y copia
  el resultado.
- `transcribir.php` y `lib/Andaluh/`: reciben el texto y aplican las reglas de
  transformación.

## Recorrido de una transcripción

1. `transcriptor.js` añade un selector de realización de las sibilantes
   (distinción, seseo o ceceo). Al pulsar **Transcribir**, envía el texto y la
   opción seleccionada como JSON mediante una petición `POST` a
   `/transcriptor/transcribir.php`.
2. `transcribir.php` lee las propiedades `texto` y `sibilant_realization`,
   carga las clases PHP y crea una instancia de `Andaluh\AndaluPao`.
3. `AndaluPao::transliterate()` pasa el texto por las reglas, una tras otra.
4. El servidor devuelve un JSON con `resultado` o, si ocurre un error, con
   `error`.
5. El JavaScript muestra el resultado y permite copiarlo al portapapeles.

Si el texto está vacío, el endpoint devuelve un resultado vacío sin ejecutar la
transcripción.

`sibilant_realization` se recibe como string en el endpoint; por ahora no se
aplica a las reglas de transliteración.

## El orden de las reglas

El orden importa: cada regla recibe el resultado de la anterior. La lista está
en `lib/Andaluh/AndaluPao.php` y se ejecuta así:

1. `HRules`
2. `XRules`
3. `ChRules`
4. `KRules`
5. `GJRules`
6. `VRules`
7. `LlRules`
8. `LRules`
9. `NsciRules`
10. `PsicoPseudoRules`
11. `VAFRules`
12. `WordEndingRules`
13. `DigraphRules`
14. `ExceptionsRules`
15. `ZzRules`
16. Restauración de `ORIGINAL_EXCEPTIONS`
17. `WordInteractionRules`

Por eso, una palabra puede cambiar en más de una regla. Las excepciones
originales se restauran al final y no se modifican; después se aplica la regla
de interacción entre palabras, que puede actuar sobre palabras vecinas.

## Reglas, una por una

Cada clase de regla está en `lib/Andaluh/` y normalmente expone un método
estático `apply(string $text): string`.

| Clase | Qué hace |
| --- | --- |
| `HRules` | Trata la `h`: en general la elimina, con tratamiento especial para combinaciones como `hua` y `hue`, y para algunas palabras excepcionales. En `ch`, no aplica esa eliminación. |
| `XRules` | Trata la `x`, con reglas distintas para una `x` entre vocales y una `x` al comienzo de palabra. Los sonidos de reemplazo se pueden configurar. |
| `ChRules` | Cambia `ch` por `x`, respetando mayúsculas y minúsculas. |
| `KRules` | Cambia `k` por `qu` ante `e` o `i`, y por `c` ante `a`, `o` o `u`, respetando mayúsculas. No aplica el cambio a palabras que empiezan por `kilo` o `kiló`. |
| `GJRules` | Cambia `g` ante `e` o `i` y trata ciertas apariciones de `j`; también transforma algunas secuencias con `bue` y `bue` seguido de `l` o `s`, conservando las mayúsculas de las vocales insertadas. |
| `VRules` | Cambia `v` por `b`; la secuencia `nv` se convierte en `mb`. Contiene excepciones para palabras concretas. |
| `LlRules` | Cambia `ll` por `y`, salvo palabras incluidas en sus excepciones. |
| `LRules` | Cambia una `l` seguida inmediatamente de ciertas consonantes por `r`. |
| `NsciRules` | Cambia cualquier subcadena `nsci` por `zzi` antes de que otras reglas transformen sus letras. |
| `PsicoPseudoRules` | Elimina la `p` inicial de las secuencias `psic...` y `pseud...`. |
| `VAFRules` | Aplica las opciones configurables de `s` y `z` ante vocal y cambia `c` por la opción de `z` ante `e` o `i`. |
| `WordEndingRules` | Trata terminaciones de palabra: entre otras, algunas terminaciones en `d`, `s`, `z`, `t`, ciertas consonantes y `y`. Las terminaciones finales `-id`, `-od` y `-ud` se convierten en `-í`, `-ó` y `-ú`, sin añadir `h`; en `-idad` y `-idades`, mantiene las transformaciones existentes salvo cuando el prefijo termina en vocal (`a`, `e`, `i`, `o`, `u`, con o sin tilde), caso en el que se convierten en `-idá` y `-idadeh`; en `-dad` y `-dades`, conserva la primera `d` cuando va precedida por una consonante, como en `verdad` → `berdá`. También tiene excepciones; `pal` conserva la `l` para que `WordInteractionRules` pueda cambiarla a `r` ante consonante. |
| `DigraphRules` | Trata grupos de consonantes en determinados contextos, por ejemplo reduciendo o modificando grupos como `st`, `ns` y otros dígrafos definidos en la clase; conserva las mayúsculas al insertar `h`. |
| `ExceptionsRules` | Aplica sustituciones de palabras completas definidas en tablas, por ejemplo `bueno` → `güeno` y `así` → `asín`, conservando las mayúsculas aunque reglas anteriores hayan alterado el patrón de capitalización. Las excepciones originales se protegen para que las demás reglas no modifiquen el reemplazo. |
| `WordInteractionRules` | Cambia la `l` al final de una palabra por `r` ante ciertas consonantes. También cambia `r` por `l` ante una `l` inicial, pero solo para las palabras que terminan en `r` y están recogidas en `WordEndingRules::WORDEND_CONST_RULES_EXCEPT`; las palabras que son resultado de una excepción original no se modifican, aunque sí se aplican las interacciones con las palabras que las rodean. |
| `ZzRules` | Reduce `zz` a `z` en las terminaciones `zzión` y `zzioneh`, que pueden aparecer como resultado de reglas anteriores. |

Las descripciones resumen el comportamiento del código: no implican que todas
las palabras o combinaciones posibles estén cubiertas.

## Mayúsculas, excepciones y configuración

- La clase `BaseRule` reúne utilidades compartidas, como conservar el tipo de
  mayúsculas y manejar vocales con tilde o circunflejo.
- Muchas reglas intentan conservar mayúsculas y minúsculas, pero cada una
  implementa ese comportamiento de forma propia.
- Las excepciones son listas explícitas dentro de las clases. Para añadir o
  cambiar una, revisa también las reglas anteriores y posteriores: otra regla
  podría transformar la palabra antes o después de que se aplique la
  excepción.
- Para cambiar frases antes de la transcripción y dejar que el resultado pase
  por todas las reglas normales, añade la pareja `'frase original' =>
  'reemplazo'` a `ExceptionsRules::PREPROCESSING_REPLACEMENTS`. Por ejemplo,
  `'para el' => 'pal'` hace que `Para el amigo de Juan` se procese como
  `Pal amigo de Juan`; este reemplazo previo no queda protegido como una
  excepción original.
- Para sustituir una palabra desde su forma original y conservar exactamente
  el reemplazo, añade la pareja `'palabra' => 'reemplazo'` a
  `ExceptionsRules::ORIGINAL_EXCEPTIONS`. Por ejemplo, `hayamos` se convierte
  en `áigamoh`; también se conserva la mayúscula inicial tanto en una palabra
  como en una frase (`Hayamos` → `Áigamoh`, `Seguidamente` → `De seguío`,
  `Cuanto Más` → `Contra mah`). Si la entrada está en mayúsculas completas,
  el reemplazo también; si solo tiene inicial mayúscula, solo se capitaliza la
  primera letra del reemplazo. El resultado de estas excepciones no se modifica
  por reglas posteriores, pero las reglas de interacción entre palabras sí
  pueden actuar sobre las palabras vecinas.
- `ExceptionsRules::EXCEPTIONS` sigue disponible para excepciones que se
  aplican al resultado de las reglas anteriores.
- `transliterate()` permite configurar `vaf_s`, `vaf_z` y `vvf`. Sus valores
  predeterminados están definidos en `BaseRule`. La página web usa esos valores
  predeterminados.

## Archivos principales

- `transcriptor.js`: eventos de los botones, petición al servidor y copia del
  texto.
- `transcribir.php`: endpoint HTTP que recibe el JSON y devuelve el resultado.
- `lib/Andaluh/AndaluPao.php`: orquesta y ordena las reglas.
- `lib/Andaluh/BaseRule.php`: clase base con utilidades compartidas y valores
  predeterminados.
- `lib/Andaluh/*Rules.php`: reglas individuales de transformación.

## Requisitos y notas para modificar el código

- El código PHP utiliza funciones multibyte (`mb_*`), por lo que el entorno PHP
  debe tener disponible la extensión `mbstring`.
- El endpoint primero busca `vendor/autoload.php`. Si no está, registra un
  autoloader sencillo para las clases de `lib/Andaluh/`.
- Para cambiar el orden, modifica la lista `$rules` en `AndaluPao.php`. Para
  investigar por qué una palabra termina de cierta forma, sigue las reglas en
  ese orden: una transformación temprana puede cambiar lo que las posteriores
  encuentran.
- Los ejemplos describen el comportamiento previsto por los patrones visibles
  en el código; para confirmar un caso concreto, prueba el texto completo en la
  aplicación, ya que los límites de palabra, las tildes y el contexto pueden
  influir.
