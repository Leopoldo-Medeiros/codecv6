## Functional Pipe — Left-to-Right Composition

Function composition is the foundation of functional programming. A `pipe()` function applies a list of callables in sequence, passing the output of each as the input of the next.

**Goal:** implement `pipe()` using `array_reduce`. It must:
- Accept any number of callables via variadic `...$fns`
- Apply them **left to right**: `pipe(f, g, h)($x)` === `h(g(f($x)))`
- Return an identity function when called with no arguments: `pipe()($x)` === `$x`

```php
$transform = pipe(
    fn(string $s) => strtolower($s),
    fn(string $s) => trim($s),
    fn(string $s) => str_replace(' ', '-', $s),
);

$transform('  Hello World  '); // → 'hello-world'
```
