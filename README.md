# TableBundle

This bundle gives a simple way to generate and manage tables based on Symfony. It's allow also :
  - Flexibility
  - Pagination (autonome)
  - Searching
  - Sorting
  - Theming

## Installation

1. Download TableBundle
2. Enable the Bundle
3. Examples

### Step 1: Download TableBundle

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

### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

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

### Dependances

* jQuery >= v1.4.2 : http://jquery.com/download/
* emc/xmlhttprequest-bundle : v3.0

## Step 3 : Exemples

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
                                    array('caption' => 'My table exemple')
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

use EMC\TableBundle\Table\TableType;
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
            'column_class' => 'btn-xs'
        ));

        $builder->add('add', 'button', array(
            'icon' => 'plus',
            'column_class' => 'btn-xs'
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
## Result
### Table
![Table ](https://cloud.githubusercontent.com/assets/2777521/9934122/8f523e56-5d4f-11e5-96dd-46322cbb505a.png)

### WebProfiler toolbar
![WebProfiler toolbar](https://cloud.githubusercontent.com/assets/2777521/9934127/94a2a3d2-5d4f-11e5-837d-2f047548526b.png)

### WebProfiler content
![WebProfiler content](https://cloud.githubusercontent.com/assets/2777521/9934132/97c2a666-5d4f-11e5-8ec9-9c4cedb57925.png)
