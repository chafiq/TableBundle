<?php

namespace EMC\TableBundle\Table\Export;

/**
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
interface ExportRegistryInterface {

    /**
     * Returns a export extension by name.<br/>
     * This methods registers the table exports extensions.<br/>
     * @param string $name The name of the export extension
     * @return Type\ExportExtensionInterface The export extension
     * @throws Exception\UnexpectedTypeException  if the passed name is not a string
     * @throws Exception\InvalidArgumentException if the export extension can not be retrieved
     */
    public function get($name);

    /**
     * Returns whether the given export extension is supported.
     * @param string $name The name of the export extension
     * @return bool Whether the export extension is supported
     */
    public function has($name);
}
