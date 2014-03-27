<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Model Entity
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Model
 */
abstract class KModelEntityAbstract extends KObjectArray implements KModelEntityInterface
{
    /**
     * List of modified properties
     *
     * @var array
     */
    protected $_modified = array();

    /**
     * The status
     *
     * Available entity status values are defined as STATUS_ constants
     *
     * @var string
     * @see Database
     */
    protected $_status = null;

    /**
     * The status message
     *
     * @var string
     */
    protected $_status_message = '';

    /**
     * Tracks if entity data is new
     *
     * @var bool
     */
    private $__new = true;

    /**
     * The identity key
     *
     * @var string
     */
    protected $_identity_key;

    /**
     * Constructor
     *
     * @param  KObjectConfig $config  An optional KObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Set the table identifier
        if (isset($config->identity_key)) {
            $this->_identity_key = $config->identity_key;
        }

        // Reset the entity
        $this->reset();

        //Set the status
        if (isset($config->status)) {
            $this->setStatus($config->status);
        }

        // Set the entity data
        if (isset($config->data)) {
            $this->setProperties($config->data->toArray(), $this->isNew());
        }

        //Set the status message
        if (!empty($config->status_message)) {
            $this->setStatusMessage($config->status_message);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional KObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'data'            => null,
            'status'          => null,
            'status_message'  => '',
            'identity_key'    => null
        ));

        parent::_initialize($config);
    }

    /**
     * Saves the entity to the data store
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function save()
    {
        if (!$this->isNew()) {
            $this->setStatus(self::STATUS_UPDATED);
        } else {
            $this->setStatus(self::STATUS_CREATED);
        }

        $this->_modified = array();
        return false;
    }

    /**
     * Deletes the entity form the data store
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function delete()
    {
        $this->setStatus(self::STATUS_DELETED);
        return false;
    }

    /**
     * Resets to the default properties
     *
     * @return KModelEntityAbstract
     */
    public function reset()
    {
        $this->_data     = array();
        $this->_modified = array();

        return $this;
    }

    /**
     * Gets the identity key
     *
     * @return string
     */
    public function getIdentityKey()
    {
        return $this->_identity_key;
    }

    /**
     * Get a property
     *
     * @param   string  $name The property name
     * @return  mixed   The property value.
     */
    public function getProperty($name)
    {
        $getter = 'getProperty'.KStringInflector::camelize($name);
        if(method_exists($this, $getter)) {
            $value = $this->$getter();
        } else {
            $value = parent::offsetGet($name);
        }

        return $value;
    }

    /**
     * Set a property
     *
     * If the value is the same as the current value and the entity is loaded from the data store the value will not be
     * set. If the entity is new the value will be (re)set and marked as modified.
     *
     * @param   string  $name       The property name.
     * @param   mixed   $value      The property value.
     * @param   boolean $modified   If TRUE, update the modified information for the property
     *
     * @return  KModelEntityAbstract
     */
    public function setProperty($name, $value, $modified = true)
    {
        if (!array_key_exists($name, $this->_data) || ($this->_data[$name] != $value))
        {
            $setter = 'setProperty'.KStringInflector::camelize($name);
            if(method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                parent::offsetSet($name, $value);
            }

            if($modified || $this->isNew()) {
                $this->_modified[$name] = $name;
            }
        }

        return $this;
    }

    /**
     * Test existence of a property
     *
     * @param  string  $name The property name.
     * @return boolean
     */
    public function hasProperty($name)
    {
        return parent::offsetExists($name);
    }

    /**
     * Remove a property
     *
     * @param   string  $name The property name.
     * @return  KModelEntityAbstract
     */
    public function removeProperty($name)
    {
        parent::offsetUnset($name);
        unset($this->_modified[$name]);

        return $this;
    }

    /**
     * Get the properties
     *
     * @param   boolean  $modified If TRUE, only return the modified data.
     * @return  array   An associative array of the entity properties
     */
    public function getProperties($modified = false)
    {
        $properties = $this->_data;

        if ($modified) {
            $properties = array_intersect_key($properties, $this->_modified);
        }

        return $properties;
    }

    /**
     * Set the properties
     *
     * @param   mixed   $data        Either and associative array, an object or a KModelEntityInterface
     * @param   boolean $modified If TRUE, update the modified information for each property being set.
     * @return  KModelEntityAbstract
     */
    public function setProperties($properties, $modified = true)
    {
        if ($properties instanceof KModelEntityInterface) {
            $properties = $properties->getProperties(false);
        } else {
            $properties = (array) $properties;
        }

        foreach ($properties as $property => $value) {
            $this->setProperty($property, $value, $modified);
        }

        return $this;
    }

    /**
     * Returns the status
     *
     * @return string The status
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Set the status
     *
     * @param   string|null  $status The status value or NULL to reset the status
     * @return  KModelEntityAbstract
     */
    public function setStatus($status)
    {
        if($status === KDatabase::STATUS_CREATED) {
            $this->__new = false;
        }

        if($status === KDatabase::STATUS_DELETED) {
            $this->__new = true;
        }

        if($status === KDatabase::STATUS_LOADED) {
            $this->__new = false;
        }

        $this->_status = $status;
        return $this;
    }

    /**
     * Returns the status message
     *
     * @return string The status message
     */
    public function getStatusMessage()
    {
        return $this->_status_message;
    }

    /**
     * Set the status message
     *
     * @param   string $message The status message
     * @return  KModelEntityAbstract
     */
    public function setStatusMessage($message)
    {
        $this->_status_message = $message;
        return $this;
    }

    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as a hash key for storing objects
     * or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        if (isset($this->_identity_key)) {
            $handle = $this->getProperty($this->_identity_key);
        } else {
            $handle = parent::getHandle();
        }

        return $handle;
    }

    /**
     * Checks if the entity is new or not
     *
     * @return bool
     */
    public function isNew()
    {
        return (bool) $this->__new;
    }

    /**
     * Check if a the entity or specific entity property has been modified.
     *
     * If a specific property name is giving method will return TRUE only if this property was modified.
     *
     * @param   string $property The property name
     * @return  boolean
     */
    public function isModified($property = null)
    {
        $result = false;

        if($property)
        {
            if (isset($this->_modified[$property]) && $this->_modified[$property]) {
                $result = true;
            }
        }
        else $result = (bool) count($this->_modified);

        return $result;
    }

    /**
     * Test if the entity is connected to a data store
     *
     * @return	bool
     */
    public function isConnected()
    {
        return false;
    }

    /**
     * Set a property
     *
     * @param   string  $property   The property name.
     * @param   mixed   $value      The property value.
     * @return  void
     */
    final public function offsetSet($property, $value)
    {
        $this->setProperty($property, $value);
    }

    /**
     * Get a property
     *
     * @param   string  $property   The property name.
     * @return  mixed The property value
     */
    final public function offsetGet($property)
    {
        return $this->getProperty($property);
    }

    /**
     * Check if a property exists
     *
     * @param   string  $property   The property name.
     * @return  boolean
     */
    final public function offsetExists($property)
    {
        return $this->hasProperty($property);
    }

    /**
     * Remove a property
     *
     * @param   string  $property The property name.
     * @return  void
     */
    final public function offsetUnset($property)
    {
        $this->removeProperty($property);
    }
}