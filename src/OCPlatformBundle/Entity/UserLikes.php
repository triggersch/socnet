<?php 

namespace PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PlatformBundle\Repository\UserLikesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserLikes{

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
   * @ORM\PrePersist
   */
  public function increase(){

    $this->getPublication()->increaseLike();
  }

  /**
   * @ORM\PreRemove
   */
  public function decrease()
  {
    $this->getPublication()->decreaseLike();
  }

  public function __construct(){

        $this->date = new \Datetime();
    }

 
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
     * @return UserLikes
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
     * Set publication
     *
     * @param \PlatformBundle\Entity\Publication $publication
     * @return UserLikes
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
     * @return UserLikes
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
