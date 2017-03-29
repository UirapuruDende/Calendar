<?php
namespace Dende\Calendar\Domain;

use DateTime;

trait SoftDeleteable
{
    /**
     * @var DateTime
     */
    protected $deletedAt;

    /**
     * Sets deletedAt.
     *
     * @param Datetime|null $deletedAt
     */
    public function setDeletedAt(DateTime $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Returns deletedAt.
     *
     * @return DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted() : bool
    {
        return null !== $this->deletedAt;
    }
}
