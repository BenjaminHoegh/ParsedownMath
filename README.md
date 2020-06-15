# ParsedownMath

![GitHub release](https://img.shields.io/github/release/BenjaminHoegh/parsedownMath.svg?style=flat-square)
![GitHub](https://img.shields.io/github/license/BenjaminHoegh/parsedownMath.svg?style=flat-square)

Latex support in [Parsedown](https://github.com/erusev/parsedown)

## Features

- Works with both Parsedown and ParsedownExtra
- Tested in php 7.0 to 7.3

## Get started

1. Make sure you have downloaded and included [Parsedown](https://github.com/erusev/parsedown) or [ParsedownExtra](https://github.com/erusev/parsedown-extra)
2. Download the [latest release](https://github.com/BenjaminHoegh/ParsedownMath/releases/latest) and include ParsedownMath.php
3. Download and include [Katex.js](https://katex.org) and [auto-render.js](https://katex.org/docs/autorender.html) your HTML

## How to write a match section

**Inline:**

- `\( ... \)`
- `$ ... $` if enabled

**Block:**

- `\[ ... \]`
- `$$ ... $$`

### Examples:

Inline

```markdown
Inline \(tag{E=mc^2}\) math

<!-- Or -->

Inline $tag{E=mc^2}$ math
```

Block

```markdown
$$
    f(x) = \int_{-\infty}^\infty
    \hat f(\xi)\,e^{2 \pi i \xi x}
    \,d\xi
$$

<!-- Or -->

\[
    f(x) = \int_{-\infty}^\infty
    \hat f(\xi)\,e^{2 \pi i \xi x}
    \,d\xi
\]
```

### Options

You can toggle math by doing the following:

```php
$Parsedown = new ParsedownMath([
    'math' => [
        'enabled' => true // Write true to enable the module
    ]
]);
```

Or if you only want inline or block you can use:

```php
'math' => [
    ['inline'] => [
        'enabled' => false // false disable the module
    ],
    // Or
    ['block'] => [
        'enabled' => false
    ]
]
```

To enable single dollar sign for inline match:

```php
$Parsedown = new ParsedownMath([
    'math' => [
        'matchSignleDollar' = true // default false
    ]
]);
```
