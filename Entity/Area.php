<?php

namespace Cis\EducationalVisitBundle\Entity;

class Area
{
    private $id;
    private $name;
    private $approvers;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getApprovers()
    {
        return $this->approvers;
    }

    public function getApprovalUsers()
    {
        $users = [];

        foreach($this->getApprovers() as $approver)
        {
            $users[] = $approver->getUser();
        }

        return $users;
    }

    public function __toString()
    {
        return $this->name;
    }
}