# ParsedownMath

![GitHub release](https://img.shields.io/github/release/BenjaminHoegh/parsedownMath.svg?style=flat-square)
![GitHub](https://img.shields.io/github/license/BenjaminHoegh/parsedownMath.svg?style=flat-square)


Latex support in [Parsedown](https://github.com/erusev/parsedown)


## How to write a match section

**Inline:**
- `\( ... \)`


**Block:**
- `\[ ... \]`
- `$$ ... $$`


### Examples:

Inline
```markdown
Inline \(tag{E=mc^2}\) math
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

You can toggle math by using `enableMath(true|false)` set it to false to disable math.

## ParsedownExtra

If you wanna use it with [ParsedownExtra](https://github.com/erusev/parsedown-extra) you need to change the following line:
```
class ParsedownMath extends Parsedown {
```
to
```
class ParsedownMath extends ParsedownExtra {
```

