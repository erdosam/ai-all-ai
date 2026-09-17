---
name: general-convention
description: This skill should be used when writing or reviewing a class (PHP8, TypeScript, Dart/Flutter) or a struct (Go) in any of our codebases — for example when the user asks to "create a class", "add a struct", "order class members", "follow our coding convention", or "apply clean code style". Applies across the PHP8, Go, Flutter, and TypeScript stacks.
version: 0.1.0
---

# General Code Style Convention

Applies to every project in the stack: PHP8, Go, Flutter (Dart), and TypeScript.

## Member visibility and ordering

- Prefer `private` over `protected` for attributes and methods; reach for `protected` only when a subclass genuinely needs to override or access the member.
- Place all attributes before all methods within a class.
- Order members top to bottom as `public`, then `protected`, then `private`.
- If a constructor exists, make it the first method, immediately after the public attributes.
- Beyond this specific rule, apply Robert C. Martin's ("Uncle Bob") Clean Code principles in general — small functions, meaningful names, single responsibility, etc.

## Per-language notes

### PHP8 and TypeScript
Both languages support `public`/`protected`/`private` modifiers directly; follow the ordering rule verbatim.
See `examples/class.php` and `examples/class.ts`.

### Go
Go has no `public`/`protected`/`private` keywords and no inheritance; the closest equivalent to public/private is an identifier's capitalization — exported (capitalized) vs. unexported (lowercase). Apply the same spirit:
- Place struct fields before methods.
- Order fields exported, then unexported; order methods exported, then unexported.
- Place the constructor function (`NewXxx`) immediately after the struct definition, before other methods.

See `examples/struct.go`.

### Flutter (Dart)
Dart has no `protected` keyword; visibility is public vs. library-private (leading underscore, e.g. `_field`). Apply the ordering rule as public-then-private, with the constructor first, following the same shape as `examples/class.ts`.

## Additional Resources

- `examples/class.php` — PHP8 class template.
- `examples/class.ts` — TypeScript class template.
- `examples/struct.go` — Go struct template with exported/unexported ordering.
