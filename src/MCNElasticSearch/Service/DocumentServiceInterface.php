<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

/**
 * Interface DocumentServiceInterface
 */
interface DocumentServiceInterface
{
    /**
     * @param $object
     * @return void
     */
    public function add($object);

    /**
     * @param $object
     * @return mixed
     */
    public function update($object);

    /**
     * @param $object
     * @return mixed
     */
    public function delete($object);
}
