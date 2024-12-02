% This homepage is based on the sphinx-design homepage: https://sphinx-design.readthedocs.io/en/furo-theme/index.html

``````{div} landing-title
:style: "padding: 0.1rem 0.5rem 0.6rem 0; background-image: linear-gradient(315deg, #438ff9 0%, #1572f4 74%); clip-path: polygon(0px 0px, 100% 0%, 100% 100%, 0% calc(100% - 1.5rem)); -webkit-clip-path: polygon(0px 0px, 100% 0%, 100% 100%, 0% calc(100% - 1.5rem));"

`````{grid}
:reverse:
:gutter: 2 3 3 3
:margin: 4 4 1 2

````{grid-item}
:columns: 12 4 4 4

<!-- ```{image} ./_static/logo_square.svg
:width: 200px
:class: sd-m-auto sd-animate-grow50-rot20
``` -->
````

````{grid-item}
:columns: 12 8 8 8
:child-align: justify
:class: sd-text-white sd-fs-3

A set of cross-platform Cocoa-compatible frameworks to easily build amazing apps and web services.

```{button-ref} about/index
:ref-type: doc
:outline:
:color: white
:class: sd-px-4 sd-fs-5
Learn more
```

````
`````

``````

```{toctree}
---
hidden: true
---

Guides/index
Manuals/index
APIRefs/index
About/index
Showcase/index
```

::::{grid} 1 2 2 3
:margin: 4 4 0 0
:gutter: 1

:::{grid-item-card} {octicon}`download` Install GNUstep
:link: Guides/Setup/index
:link-type: doc

Install GNUstep on your system, and get started in your IDE.
:::

:::{grid-item-card} {octicon}`paintbrush` Customize
:link: Guides/Customize/index
:link-type: doc

Customize GNUstep to suit your needs.
:::

:::{grid-item-card} {octicon}`terminal` Learn Objective-C
:link: Guides/ObjC/index
:link-type: doc

Learn Objective-C, the preferred programming language for GNUstep. Build simple command-line tools.
:::

:::{grid-item-card} {octicon}`rocket` Build Apps
:link: Guides/App/index
:link-type: doc

Build example apps to learn the GNUstep framework.
:::

:::{grid-item-card} {octicon}`squirrel` Port Apps
:link: Guides/Port/index
:link-type: doc

Bring your Mac apps to other platforms.
:::

:::{grid-item-card} {octicon}`terminal` API references
:link: APIRefs/index
:link-type: doc

Browse the APIs available for your apps and tools to use.
:::

::::