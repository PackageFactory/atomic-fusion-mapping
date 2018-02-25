# PackageFactory.AtomicFusion.Mapping

> Apply `Neos.Neos:ContentElementWrapping` automatically during iteration
> with `Neos.Fusion:Collection` if nodes are mapped with `PackageFactory.AtomicFusion:NodeMapping`
> instead of `Neos.Fusion:RawArray`.
>
> This allows to use arrays of shapes as clean interface for list
> components but still map editable nodes to such componentzs.
> This even allows to combine editable and not editable items in a
> single collection.

## Status

**This is currently experimental code so do not rely on any part of this.**

### Authors & Sponsors

* Martin Ficzel - ficzel@sitegeist.de

*The development and the public-releases of this package is generously sponsored by our employer http://www.sitegeist.de.*

## How it works

This package adds a prototype `PackageFactory.AtomicFusion:NodeMapping`
which extends `Neos.Fusion:RawArray` by returning a
`\PackageFactory\AtomicFusion\Mapping\Domain\Model\NodeMapping` wrapper
object that wraps the current data and stores an additional reference to the
currently rendered node.

If the `Neos.Fusion:Collection` iterates and detects an NodeMapping as
current that `Neos.Neos:ContentElementWrapping` is applied to the
content of the iteration automatically.

### Prototypes

- `PackageFactory.AtomicFusion:NodeMapping`: Extend `Neos.Fusion:RawArray` and
  return `\PackageFactory\AtomicFusion\Mapping\Domain\Model\NodeMapping`
  which wraps the array-data and stores and expects and store a reference
  to `nodeInterface` on key `node`.
- `PackageFactory.AtomicFusion:GetContext` get the property with the
  name specified in `property` from the current fusion context.

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

The fusion mapping takes the list nodes an maps then via
`PackageFactory.AtomicFusion:NodeMapping` which stores the needed node
reference for later automatic contentElementWrapping in `Neos.Fusion:Collections`.

Please note that the mapping of editable properties for the children of
the NodeList happens directly in the main prototype.


```
prototype(Vendor.Plugin:NodeList) < prototype(Neos.Neos:ContentComponent) {

    renderer = Vendor.Plugin:Component.List {

        title = Neos.Neos:Editable {
            property = 'title'
            block = 'false'
        }

        items = Neos.Fusion:RawCollection {
            collection = ${q(node).children().get()}
            itemName = 'node'
            itemRenderer = PackageFactory.AtomicFusion:NodeMapping {
                node = ${node}

                title = Neos.Neos:Editable {
                    property = 'title'
                    block = 'false'
                }

                description = Neos.Neos:Editable {
                    property = 'description'
                    block = 'false'
                }
            }

            @process.prependStaticItem = ${Array.unshift(value, {title: 'Foo', description: 'bar'})}
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
prototype(Vendor.Plugin:Component.List) < prototype(Neos.Fusion:Component) {

    @styleguide {
        props {
            title = 'My List'
            items = Neos.Fusion:RawCollection {
                1 = ${{title:'Title 1', description: 'Description 1' }}
                2 = ${{title:'Title 2', description: 'Description 2' }}
                3 = ${{title:'Title 3', description: 'Description 3' }}
            }
        }

    }

    title = null
    items = null

    renderer = afx`
        <div>
            <h1>{props.title}</h1>
            <Neos.Fusion:Collection collection={props.items} @children="itemRenderer" itemName="listItem">
                <Vendor.Plugin:Component.List.Item title={listItem.title} description={listItem.description} />
            </Neos.Fusion:Collection>
            <strong @if.hasNoItems={props.items ? false : true}>no items found</strong>
        </div>
    `
}

prototype(Vendor.Plugin:Component.List.Item) < prototype(Neos.Fusion:Component) {

    title = null
    description = null

    renderer = afx`
        <dl><dt>{props.title}</dt><dd>{props.description}</dd></dl>
    `
}
```

## License

see [LICENSE file](LICENSE)
