<?php

namespace Gregoriohc\Seedable;

trait IsSeedable
{
    /**
     * @return \Illuminate\Support\Collection|static[]
     */
    abstract public function seedData();

    /**
     * @param array $item
     * @return \Illuminate\Support\Collection|static[]
     */
    abstract public function seedUpdate($item);

    /**
     * @param array $item
     * @return \Illuminate\Support\Collection|static[]
     */
    abstract public function seedCreate($item);

    /**
     * @param null|array $items
     * @param bool $update
     * @return bool
     */
    public function seed($items = null, $update = true)
    {
        if (!$items && $this instanceof IsSeedable) {
            $items = $this->seedData();
        }

        if (!$items) {
            return false;
        }

        foreach ($items as $item) {
            $update ? $this->seedUpdate($item) : $this->seedCreate($item);
        }

        return true;
    }
}
