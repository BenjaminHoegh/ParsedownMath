# ParsedownMath
Latex support in Parsedown


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




## Options

You can toggle math by using `enableMath(true|false)` set it to false to disable math.
