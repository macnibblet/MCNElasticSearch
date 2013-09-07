<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNElasticSearch\Service;

/**
 * Interface MappingServiceInterface
 */
interface MappingServiceInterface
{
    /**
     * Build the mapping of all or a list of given types
     *
     * @param array $types List of type names to build
     *
     * @return void
     */
    public function build(array $types = null);

    /**
     * Delete the entire mapping or a specific part
     *
     * @param array $types
     *
     * @return void
     */
    public function delete(array $types = null);
}
