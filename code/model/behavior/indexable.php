<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Indexable Model Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Behavior
 */
class ModelBehaviorIndexable extends ModelBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param   ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.fetch', '_buildQuery')
             ->addCommandCallback('before.count', '_buildQuery');
    }

    /**
     * Insert the model states
     *
     * @param ObjectMixable $mixer
     */
    public function onMixin(ObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if ($mixer instanceof ModelDatabase)
        {
            $table = $mixer->getTable();

            // Set the dynamic states based on the unique table keys
            foreach ($table->getUniqueColumns() as $key => $column) {
                $mixer->getState()->insert($key, $column->filter, null, true, $table->mapColumns($column->related, true));
            }
        }
    }

    /**
     * Add order query
     *
     * @param   ModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _buildQuery(ModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof ModelDatabase)
        {
            //Get only the unique states
            $states = $context->state->getValues(true);

            if (!empty($states))
            {
                $columns = array_intersect_key($states, $model->getTable()->getColumns());
                $columns = $model->getTable()->mapColumns($columns);

                foreach ($columns as $column => $value)
                {
                    if (isset($value))
                    {
                        $context->query->where('tbl.' . $column . ' ' . (is_array($value) ? 'IN' : '=') . ' :' . $column)
                            ->bind(array($column => $value));
                    }
                }
            }
        }
    }
}