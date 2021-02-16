<?php

namespace Althingi\Model;

use DateTime;

class Plenary implements ModelInterface
{
    /** @var int */
    private $plenary_id;

    /** @var int */
    private $assembly_id;

    /** @var \DateTime */
    private $from;

    /** @var \DateTime */
    private $to;

    /** @var string */
    private $name;

    /**
     * @return int
     */
    public function getPlenaryId(): int
    {
        return $this->plenary_id;
    }

    /**
     * @param int $plenary_id
     * @return Plenary
     */
    public function setPlenaryId(int $plenary_id): Plenary
    {
        $this->plenary_id = $plenary_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return Plenary
     */
    public function setAssemblyId(int $assembly_id): Plenary
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     * @return Plenary
     */
    public function setFrom(DateTime $from = null): Plenary
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param \DateTime $to
     * @return Plenary
     */
    public function setTo(DateTime $to = null): Plenary
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Plenary
     */
    public function setName(string $name = null): Plenary
    {
        $this->name = $name;
        return $this;
    }

    public function toArray()
    {
        return [
            'plenary_id' => $this->plenary_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from ? $this->from->format('Y-m-d H:i') : null,
            'to' => $this->to ? $this->to->format('Y-m-d H:i') : null,
            'name' => $this->name,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
