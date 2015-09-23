# TableBundle

This bundle gives a simple way to generate and manage tables based on Symfony. It's allow also :
  - Flexibility
  - Pagination (automated)
  - Searching
  - Sorting
  - Theming
  - Extensions
  - Sub-tables (automated)

## Installation

1. Download TableBundle
2. Enable the Bundle
3. Create/Custom new column type extension
4. Sub-tables
5. Examples
6. Result & Screenshots

### Download TableBundle

This can be done in several ways, depending on your preference. The first method is the standard Symfony2 method.

**Using Composer**

Add TableBundle in your composer.json:

```
{
    "require": {
        "emc/table-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update emc/table-bundle
```

**Using submodules**

If you prefer instead to use git submodules, then run the following:

``` bash
$ git submodule add https://github.com/chafiq/TableBundle.git vendor/emc/table-bundle/EMC/TableBundle/
$ git submodule update --init
```

Note that using submodules requires manually registering the `EMC` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'EMC' => __DIR__.'/../vendor/bundles',
));
```

### Enable the bundle

Finally,

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EMC\TableBundle\EMCTableBundle(),
    );
}
```

Enable the routing config :

``` yaml
# app/config/routing.yml
emc_table:
    resource: "@EMCTableBundle/Resources/config/routing.yml"
    prefix:   /
```

### Dependances

* jQuery >= v1.4.2 : http://jquery.com/download/
* emc/xmlhttprequest-bundle : 3.0

## Create/Custom new column type extension

PHP : Column type class
``` php
<?php
namespace Acme\MyBundle\Table\Column;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomType extends ColumnType {
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
        /* Add you column data view here. You can access to them in the twig extension widget */
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        /* define you parameters here */
    }
    public function getName() {
        return 'custom';
    }
}
```

Twig : Column type template
``` twig
{# Acme/MyBundle/Resources/views/Table/Column/custom.html.twig #}
{% block custom_widget %}
    {# here you can edit the content of TD element (Cell). #}
{% endblock %}
```

Config : Column type service
``` yaml
# Acme/MyBundle/Resources/config/services.yml
services:
    ...
    my_custom_column_type:
        class: Acme\MyBundle\Table\Column\CustomType
        tags:
            -  { name: table.type, alias: custom }
```

## Sub-tables

Controller Code
``` php
        /* Controller */
        ...
        $table = $factory->create(new MyTableType(), null, array(
                        ...
                        'caption' => 'My sub table example',
                        'subtable'  => new MySubTableType(),
                        'subtable_params'   => array('cityId' => 'c.id'),
                        'subtable_options'  => array( /* can take the same options as the root table */ )
                        ...
      );
```

Table Type Code
``` php

<?php

namespace Acme\MyBundle\Table\Type;

use EMC\TableBundle\Table\Type\TableType;
use EMC\TableBundle\Table\TableBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MySubTableType extends TableType {
    
    public function buildTable(TableBuilderInterface $builder, array $options) {
        $builder->add('store', 'text', array(
            'params' => array('ci.name'),
            'title' => 'Center of interest',
            'allow_filter' => true,
            'allow_sort' => true
        ));

        $builder->add('address', 'text', array(
            'params' => array('ci.address', 'ci.zipcode', 'c.name'),
            'format' => '%s %s %s',
            'title' => 'Address',
            'allow_filter' => true,
            'allow_sort' => true
        ));
        
        $builder->add('delete', 'button', array(
            'icon' => 'remove',
            'attrs' => class('attrs' => 'btn-xs')
        ));

        $builder->add('add', 'button', array(
            'icon' => 'plus',
            'attrs' => class('attrs' => 'btn-xs')
        ));
    }

    public function getName() {
        return 'my-sub-table';
    }

    public function getQueryBuilder(ObjectManager $entityManager, array $params) {
        return $entityManager
                    ->getRepository('AcmeMyBundle:CenterInterest')
                        ->createQueryBuilder('ci')
                        ->innerJoin('ci.city', 'c')
                        ->where('c.id = :cityId')
                        ->setParameter('cityId', $params['cityId']); /* this parameter was defined in the subtable_options. $params is poputated in the TableType::buildSubtableParams and are passed to this method */
    }
}

    
```


## Examples

Consider that we have two data base tables :
  - city : #id, name, createdAt, stateid
  - state : #id, name

### Controller Code
``` php
        use Symfony\Component\HttpFoundation\Request;
        use Acme\MyBundle\Table\Type\MyTableType

        public function indexAction(Request $request) {
            
            /* @var $factory \EMC\TableBundle\Table\TableFactory */
            $factory = $this->get('table.factory');
          
            $table = $factory   ->create(
                                    new MyTableType(),
                                    null,
                                    array('caption' => 'My table example')
                                )
                                ->getTable();
          
          return $this->render('AcmeMyBundle:Table:index.html.twig', array('table' => $table));
        }
```

### Template Code

``` twig			
...
    {% stylesheets 'bundles/emctable/css/style.css' filter='cssrewrite' %}
        <link rel="stylesheet" href="{{ asset_url }}" />
    {% endstylesheets %}

    {% javascripts 'bundles/emctable/js/EMCTable.js' %}
        <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}

    <div class="container">{{ table(table) }}</div>
...
```

### Table Type Code

``` php

<?php

namespace Acme\MyBundle\Table\Type;

use EMC\TableBundle\Table\Type\TableType;
use EMC\TableBundle\Table\TableBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MyTableType extends TableType {
    
    public function buildTable(TableBuilderInterface $builder, array $options) {
        $builder->add('id', 'anchor', array(
            'route' => 'edit_route',
            'params' => array('id' => 'c.id'),
            'format' => '#%s',
            'title' => '#',
            'allow_sort' => true
        ));

        $builder->add('state', 'text', array(
            'params' => array('s.name'),
            'format' => '%s',
            'title' => 'State',
            'allow_filter' => true,
            'allow_sort' => true
        ));

        $builder->add('city', 'text', array(
            'params' => array('c.name', 'c.id'),
            'format' => '%s (#%d)',
            'title' => 'City',
            'allow_filter' => true,
            'allow_sort' => true
        ));

        $builder->add('createdAt', 'datetime', array(
            'params' => array('t.createdAt'),
            'title' => 'Date',
            'allow_sort' => true
        ));

        $builder->add('delete', 'button', array(
            'icon' => 'remove',
            'text' => 'Delete',
            'attrs' => class('attrs' => 'btn-xs')
        ));

        $builder->add('add', 'button', array(
            'icon' => 'plus',
            'attrs' => class('attrs' => 'btn-xs')
        ));

        $builder->add('status', 'icon', array(
            'params' => array('c.id'),
            'format' => function($id) { return $id % 2 ? 'star' : 'star-o'; }
        ));

        $builder->add('pdf', 'image', array(
            'asset_url' => 'bundles/acmesandbox/images/pdf.jpg'
        ));
    }

    public function getName() {
        return 'my-table';
    }

    public function getQueryBuilder(ObjectManager $entityManager, array $options) {
        return $entityManager
                    ->getRepository('AcmeMyBundle:City')
                        ->createQueryBuilder('c')
                        ->innerJoin('t.state', 's');
    }
}

    
```
## Result & Screenshots
### Table
![Table ](https://cloud.githubusercontent.com/assets/2777521/9934122/8f523e56-5d4f-11e5-96dd-46322cbb505a.png)

### WebProfiler toolbar
![WebProfiler toolbar](https://cloud.githubusercontent.com/assets/2777521/9934127/94a2a3d2-5d4f-11e5-837d-2f047548526b.png)

### WebProfiler content
![WebProfiler content](https://cloud.githubusercontent.com/assets/2777521/9934132/97c2a666-5d4f-11e5-8ec9-9c4cedb57925.png)
