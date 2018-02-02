# PackageFactory.AtomicFusion.Mapping

> Apply `Neos.Neos:ContentElementWrapping` automatically if Nodes are mapped with
> `Neos.Fusion:RawCollection` and are rendered with `Neos.Fusion:Collection`
> afterwards

## Status

**This is currently experimental code so do not rely on any part of this.**

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public-releases of this package is generously sponsored by our employer http://www.sitegeist.de.*

## How it works

This package will store a reference to the currently rendered Node in
the array-key `__node` whenever `Neos.Fusion:RawCollection` maps a Node
to an Array.

If the `Neos.Fusion:Collection` iterates and detects an array-item that
has the key `__node` `Neos.Neos:ContentElementWrapping` is applied to the
content automatically.

## Usage

### NodeTypes

```yaml
#
# A content node that can have a number of child nodes
#
'Vendor.Site:NodeList':
  ui:
    label: 'Node List'
    group: 'general'
  superTypes:
    'Neos.Neos:Content': TRUE
  constraints:
    nodeTypes:
      'Vendor.Site:NodeList.Item': TRUE
      '*': FALSE
  properties:
    title:
      type: 'string'
      defaultValue: 'Title'
      ui:
        inlineEditable: true

#
# A child node for the content above that cannot be used anywhere else
#
'Vendor.Site:NodeList.Item':
  ui:
    label: 'Node List Item'
    group: 'general'
  superTypes:
    'Neos.Neos:Content': TRUE
  properties:
    title:
      type: 'string'
      defaultValue: 'Title'
      ui:
        inlineEditable: true
    description:
      type: 'string'
      defaultValue: 'Description'
      ui:
        inlineEditable: true
```

### Fusion Mapping

The fusion mapping takes the nodes an mapps then via
`Neos.Fusion:RawArray`. The mapping for the child nodes happens
directly in the main mapping (this can be done differently if needed).


```
prototype(Vendor.Site:NodeList) < prototype(Neos.Neos:ContentComponent) {

    renderer = Vendor.Site:Component.List {

        title = Neos.Neos:Editable {
            property = 'title'
            block = 'false'
        }

        items = Neos.Fusion:RawCollection {
            collection = ${q(node).children().get()}
            itemName = 'node'
            itemRenderer = Neos.Fusion:RawArray {

                title = Neos.Neos:Editable {
                    property = 'title'
                    block = 'false'
                }

                description = Neos.Neos:Editable {
                    property = 'description'
                    block = 'false'
                }
            }
        }
    }
}

```

### Fusion Presentation

The fusion presentation does not care about `Neos.Neos:ContentElementWrapping`
at all and plainly iterates over the passed array. That way the same
presentational component can be used to render an inline editable list
of nodes or some other data that was aquired via php.

```
prototype(Vendor.Site:Component.List) < prototype(Neos.Fusion:Component) {

    @styleguide {
        props {
            title = 'My List'
            items = Neos.Fusion:RawCollection {
                1 = ${ {title:'Title 1', description: 'Description 1' } }
                2 = ${ {title:'Title 2', description: 'Description 2' } }
                3 = ${ {title:'Title 3', description: 'Description 3' } }
            }
        }
    }

    title = null
    items = null

    renderer = afx`
        <div>
            <h1>{props.title}</h1>
            <Neos.Fusion:Collection collection={props.items} @children="itemRenderer" itemName="listItem">
                <Vendor.Site:Component.List.Item title={listItem.title} description={listItem.description} />
            </Neos.Fusion:Collection>
            <strong @if.hasNoItems={props.items ? false : true}>no items found</strong>
        </div>
    `
}

prototype(Vendor.Site:Component.List.Item) < prototype(Neos.Fusion:Component) {

    title = null
    description = null

    renderer = afx`
        <dl><dt>{props.title}</dt><dd>{props.description}</dd></dl>
    `
}
```

## License

see [LICENSE file](LICENSE)
