MCNElasticSearch
================
[![Build Status](https://travis-ci.org/macnibblet/MCNElasticSearch.png?branch=master)](https://travis-ci.org/macnibblet/MCNElasticSearch)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/macnibblet/MCNElasticSearch/badges/quality-score.png?s=d59201563ad576325e25ddd75988518f6066f48f)](https://scrutinizer-ci.com/g/macnibblet/MCNElasticSearch/)
[![Code Coverage](https://scrutinizer-ci.com/g/macnibblet/MCNElasticSearch/badges/coverage.png?s=5daa1dfdd985325130d299d6f0d95e6563c254d5)](https://scrutinizer-ci.com/g/macnibblet/MCNElasticSearch/)

This is a reasonably simply module that will assist keeping your elastic search index up to date with your database.

You wish to...
--------------

* You wish to have a utility for updating / deleting the mapping
* You wish to keep your elastic search synchronized with your ORM
* You wish to have a service that provides you with a simple interface to search and return doctrine entities


Step 1, setup mapping
---------------------

Start by copying the file ```config/MCNElasticSearch.global.php``` to your ```config/autoload/``` directory.
The types array is a associative array name => mapping information. For all options in mapping check the
```MCNElasticSearch\Options\TypeMappingOptions``` currently only basic options are available but PRs are welcome!

Example configuration
```php
return [
    'MCNElasticSearch' => [
        'metadata' => [

            /**
             * List of object mappings
             */
            'objects' => [
                'Company\Entity\CompanyEntity' => [
                    'hydrator' => 'company',
                    'type'     => 'companies',
                    'index'    => 'example',
                ]
            ],

            /**
             * List of types E.g "SQL Tables"
             */
            'types' => [
                'companies' => [
                    'index'      => 'example',
                    'source'     => ['enabled' => false],
                    'properties' => [
                        'id'      => ['type' => 'integer'],
                        'name'    => ['type' => 'string'],
                        'address' => [
                            'type'       => 'object',
                            'properties' => [

                                'id'   => ['type' => 'integer'],
                                'type' => ['type' => 'string', 'not_analyzed' => true],

                                'street'         => ['type' => 'string'],
                                'zipcode'        => ['type' => 'integer'],
                                'country'        => ['type' => 'string'],
                                'coordinates'    => ['type' => 'geo_point'],
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
```

Now that you have setup your mapping we need to run it against our elastic search
```php public/index.php es mapping create```
And if you wish to delete it
```php public/index.php es mapping delete```

Step 2, Setup a synchronizer
----------------------------

Now we need to implement the synchronizer, and this is dead simple!
```php
class ElasticSearchSynchronizer extends \MCNElasticSearch\Listener\AbstractDoctrineORMSynchronizer
{
    /**
     * Check that an object is of the proper instance
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function isValid($object)
    {
        return $object instanceof CompanyEntity;
    }
}
```

You will also need to setup a factory and pass an instance of ```MCNElasticSearch\Service\DocumentService``` but that
is hopefully something that can be removed in the future!

Now we need to tell doctrine publish events to your synchronizer. So in your doctrine configuration you need to add
```php
    'eventmanager' => [
        'orm_default' => [
            'subscribers' => [
                ElasticSearchSynchronizer::class
            ]
        ]
    ],
```

Step 3, Perform a search
------------------------

Now im going to continue on the previous example, and take a piece of code from my API written using ```PhlyRestfully```
and perform a search against my companies type sorting by distance and filtering away all companies further away then 1000km

```php
class CompanyResource implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \Company\Service\CompanyServiceInterface
     */
    protected $companyService;

    /**
     * @var \MCNElasticSearch\Service\SearchServiceInterface
     */
    protected $searchService;

    /**
     * @param CompanyServiceInterface $companyService
     * @param SearchServiceInterface $searchService
     */
    public function __construct(CompanyServiceInterface $companyService, SearchServiceInterface $searchService)
    {
        $this->searchService  = $searchService;
        $this->companyService = $companyService;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetchAll', [$this, 'fetchAll']);
    }

    /**
     * @param ResourceEvent $event
     * @return \PhlyRestfully\ApiProblem|\Zend\Paginator\Paginator
     */
    public function fetchAll(ResourceEvent $event)
    {
        $coordinates =       $event->getQueryParam('coordinates');
        $maxDistance = (int) $event->getQueryParam('distance', 1000);

        $sort = [
            '_geo_distance' => [
                'companies.address.coordinates' => $coordinates,
                'unit'  => 'km',
                'order' => 'asc'
            ]
        ];

        $geoDistanceFilter = new GeoDistance('companies.address.coordinates', $coordinates, $maxDistance . 'km');

        $query = new Query();
        $query->addSort($sort);
        $query->setFilter($geoDistanceFilter);

        return $this->searchService->search(CompanyEntity::class, $query, SearchServiceInterface::HYDRATE_DOCTRINE_OBJECT);
    }
}
```

Step 4, Hallelujah moment
-------------------------
Profits!
