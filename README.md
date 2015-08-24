# CIC October Module

### Config Imports

As you know, YAML is not dynamic and it has no way of pulling in data from other YAML files. So, to remedy this, we have a little special key called `@import`. You can use this to "absorb" other config files. 

For example, given these files:

**top-level.yaml**

```yaml
colors:
  - yellow
  - brown

animals:
  - cow
  - pig
  - pigeon
```

**meats.yaml**

```yaml
fish:
  - salmon
  - cod
  - herring
pork:
  - bacon
  - ham
```

**start.yaml**

```yaml
@import: ~/modules/castiron/tests/samples/top-level.yaml

colors:
  - green
  - orange

foods:
  dairy:
    - milk
    - cheese
    - yogurt
  meats:
    @import:
      - ~/modules/castiron/tests/samples/meats.yaml

```

After the import, **start.yaml** becomes:

```yaml
colors:           # notice that imported values are overwritten
  - green 
  - orange

animals:
  - cow
  - pig
  - pigeon

foods:
  dairy:
    - milk
    - cheese
    - yogurt
  meats:
    fish:         # notice how import values are placed
      - salmon
      - cod
      - herring
    pork:
      - bacon
      - ham
```

NOTES:

1. The `@import` key is not something special in YAML, it's our own convention. In fact, it's just a key.  
1. You can import multiple files at once.  They are imported in order.
1. File paths are resolved using October CMS's symbols. 


