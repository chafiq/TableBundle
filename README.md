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
          
          $table = $factory->create(new MyTableType())->getTable();
          
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

use EMC\TableBundle\Column;
use EMC\TableBundle\Table\TableType;
use EMC\TableBundle\Table\TableBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MyTableType extends TableType {
    
    public function buildTable(TableBuilderInterface $builder, array $options) {
        $column = new Column\Anchor('#', array('cid' => 'c.id'));
        $column->setRoute('acme_my_edit');
        $column->setTitle('Ouvrir');
        $builder->addColumn($column);
                
        $column = new Column\Text('Pays', array('s.name'));
        $column->setSearchable(true);
        $column->setSortable(true);
        $builder->addColumn($column);
        
        $column = new Column\Text('Ville', array('c.name'));
        $column->setSearchable(true);
        $column->setSortable(true);
        $builder->addColumn($column);

        $column = new Column\Date('date', array('c.createdAt'));
        $column->setSortable(true);
        $builder->addColumn($column);
        
        $action = new Column\Action();
        
        $column = new Column\Button('delete', array('c.id'));
        $column->setIcon('trash');
        $action->addColumn($column);
        
        $column = new Column\Button('edit', array('c.id'));
        $column->setIcon('edit');
        $column->setText('Editer');
        $action->addColumn($column);
        
        $builder->addColumn($action);
        
        $builder->setCaption('Liste des villes');
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
