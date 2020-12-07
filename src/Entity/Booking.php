<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("datetime")
     * @Assert\GreaterThan("today", message="La date d'arrivée sur les lieux doit être ultérieure à la date d'aujourd'hui")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("datetime")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ des lieux doit être plus éloignée que la date d'arrivée sur les lieux")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Permet de remplir automatiquement les champs qui ne sont pas dans le formulaire de réservation
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)){
            // prix de l'annonce * nombre de jour
            $this->amount = $this->ad->getPrice() * $this->getDuration(); 
        }
    }

    /**
     * Permet de récup le nombre de jour entre 2 dates
     */
    public function getDuration(){
        // différence entre objet DateTime -> méthode diff() va renvoyer un objet DateInterval
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days; // renvoie le nombre de jour d'un objet DateInterval
    }

    /**
     * Permet de savoir si la date est bonne où non
     */
    public function isBookableDates()
    {
        // connaître les dates impoossible pour l'annonce (voir dans Ad)
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // comparer les dates choisies avec les dates impossible (fonction juste en dessous)
        $bookingDays = $this->getDays();

        // transformation des objets dateTime en tableau de chaines de caracères pour les journées (faciliter la comparaison)
        $days = array_map(function($day){
            return $day->format('Y-m-d');
        },$bookingDays);

        $notAvailable = array_map(function($day){
            return $day->format('Y-m-d');
        },$notAvailableDays);

        foreach($days as $day){
            if(array_search($day,$notAvailable) !== false) return false;
        }

        return true;

    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     *
     * @return array Un tableau d'objets DateTime représentant les jours de la réservation (notre objet)
     */
    public function getDays()
    {
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );
        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d',$dayTimestamp));
        },$resultat);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
