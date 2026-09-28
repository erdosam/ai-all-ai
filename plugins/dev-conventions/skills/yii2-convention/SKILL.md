---
name: yii2-convention
description: This skill should be used when generating or reviewing PHP code in a Yii2 (advanced template) application — for example when the user asks to "create a use case", "create a business logic model", "add a Yii2 controller action" for a non-CRUD endpoint, "create a Yii2 module", "add a service class", "follow Yii2 convention", or generates code touching a `backend/modules/**/models`, `frontend/modules/**/models`, `console/modules/**/models`, or the corresponding `**/controllers` folder.
version: 0.3.0
---

# Yii2 Application Convention

Defines how Yii2 (advanced template) applications are organized — applications, modules, services, use cases, and controllers — and the exact code shape to generate for each.

## Core Concepts

### Application
The advanced template ships three applications: `backend`, `frontend`, `console`. `common` is not an application; other applications may be added per project (e.g. `api`). Use the alias `@app` to refer to the current application in convention text and paths; when generating code, replace `@app` with the concrete application name (`backend`, `frontend`, etc.) in namespaces and file paths.

### Module
A module groups features that share a purpose (e.g. `loyalty`, covering collect, redeem, and rule management). Nest module folders as:
`@app[/modules/<MODULE-NAME>[/modules/<SUB-MODULE-NAME>[..]]]`

### Service
A service handles non-business logic: external I/O such as cloud storage uploads or third-party API calls. Inject services into use cases through `__construct()`.

### Constructor property promotion (PHP8)
Any class extending `yii\base\BaseObject` — directly, or via `Component`/`Model`, which covers both Service and Use Case classes — declares its injected dependencies as typed, `private`, promoted constructor properties, instead of separate property declarations with manual assignment. Keep the trailing `$config = []` parameter and forward it to `parent::__construct($config)`, since `Yii::createObject()` and Yii2's `Yii::configure()` mechanism rely on it:

```php
public function __construct(private MyType $att1, private MyType2 $att2, $config = [])
{
    parent::__construct($config);
}
```

This applies to injected collaborators only. A use case's public, request-bound attributes (populated by `load()`, validated by `rules()`) stay plain public properties, not constructor parameters.

### Use Case (Business Logic Model)
A use case — the term used interchangeably with "business logic model" in this codebase — handles exactly one non-CRUD business process behind one API endpoint: an operation that is more than create/read/update/delete on a single entity, typically validating and orchestrating across more than one entity and/or service. Plain single-entity CRUD does not need a use case; expose it directly through Yii2's standard REST/ActiveRecord controller instead.

- Location: `@app[/modules/<MODULE-NAME>[/modules/<SUB-MODULE-NAME>[..]]]/models/`.
- Usable only by controllers within the same module (not submodules) and by its own test class.
- Extends `yii\base\Model`.
- Public attributes represent the inputs the operation needs; `rules()` declares validators for each attribute.
- Exposes exactly one public method, `execute()`, which validates first (`$this->validate()`), then runs the logic. Split large logic into private subprocess methods.
- Each subprocess should throw `yii\base\Exception` on failure; `execute()` catches it and reports via `$this->addError()`.
- Never throws an HTTP exception (`yii\web\HttpException` or a subclass) — a use case only knows business/validation failure, not HTTP status codes. It reports failure by returning `false` from `execute()` with errors added; only the controller decides what HTTP response that becomes.
- Depends on services injected via `__construct()`, using PHP8 constructor property promotion (see "Constructor property promotion" above).
- Implements `toArray()` to define the HTTP response payload.

Generate use cases following `examples/business-logic-model.php`.

### Test unit for a use case
- File: `@app/tests/unit/[<MODULE-NAME>_[<SUB-MODULE-NAME>_[..]]][MODEL-NAME]Test.php` (e.g. `Loyalty_CollectRewardCalculationTest.php`, `Loyalty_Manager_CollectRewardRuleTest.php`).
- Extends `Codeception\Test\Unit`.
- Builds the use case with `Yii::createObject()`, sets attributes with `setAttributes()`, calls `execute()`, then asserts the result with `verify()`.

Generate tests following `examples/model-test.php`.

### Controller
Groups actions where each action uses exactly one use case.

- Name controllers with at most 2 words, since the name becomes a URL path segment; name it after the sub-feature (e.g. `CollectController` inside the `loyalty` module).
- Guard every action with an `AccessControl` rule using permission name `@app[_<module-name>[_<sub-module-name>[..]]]:<controller-name>::<action-name>` (e.g. `frontend_loyalty:collect::calculate`).
- Action body: build the model with `Yii::createObject()`, load request data with `load($this->request->post(), "")` (empty scenario string, since it's a REST API), call `execute()`, and return the model — Yii2 serializes it through `toArray()`.
- Owns all HTTP-level error handling: the controller is the only place allowed to throw `yii\web\HttpException` (or a subclass, e.g. `NotFoundHttpException`) when an action needs to surface a specific HTTP status.

Generate controllers following `examples/controller.php`.

## Workflow

1. Identify the module (and submodule, if any) the feature belongs to.
2. Confirm the feature actually needs a use case — a new API endpoint whose process is more than plain CRUD on one entity. If it's plain CRUD, use Yii2's standard REST/ActiveRecord controller instead and skip the rest of this workflow.
3. Create the use case under `<module>/models/`, following `examples/business-logic-model.php`.
4. Create its test under `@app/tests/unit/`, following `examples/model-test.php`.
5. Add or extend the controller under `<module>/controllers/`, following `examples/controller.php`, wiring the new action's permission into `behaviors()`.

## Additional Resources

- `examples/business-logic-model.php` — use case template (attributes, `rules()`, `execute()`, `toArray()`).
- `examples/controller.php` — controller template with `AccessControl` and `VerbFilter`.
- `examples/model-test.php` — Codeception unit test template for a use case.
