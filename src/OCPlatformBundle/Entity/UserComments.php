<?php

namespace PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PlatformBundle\Repository\UserCommentsRepository")
 */
class UserComments{

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
    * @var \DateTime
    *
    * @ORM\Column(name="date", type="datetime")
    */
  private $date;

  /**
   * @ORM\Column(name="textcomment", type="text")
   */
  private $textcomment;

  /**
   * @ORM\ManyToOne(targetEntity="PlatformBundle\Entity\Publication")
   * @ORM\JoinColumn(nullable=false)
   */
  private $publication;

  /**
   * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   */
  private $user;
  

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return UserComments
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set textcomment
     *
     * @param string $textcomment
     * @return UserComments
     */
    public function setTextcomment($textcomment)
    {
        $this->textcomment = $textcomment;

        return $this;
    }

    /**
     * Get textcomment
     *
     * @return string 
     */
    public function getTextcomment()
    {
        return $this->textcomment;
    }

    /**
     * Set publication
     *
     * @param \PlatformBundle\Entity\Publication $publication
     * @return UserComments
     */
    public function setPublication(\PlatformBundle\Entity\Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get publication
     *
     * @return \PlatformBundle\Entity\Publication 
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     * @return UserComments
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
