<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $uid = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Card::class)]
    private Collection $hand;

    public function __construct()
    {
        $this->hand = new ArrayCollection();
        $this->uid = uniqid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function normalize($showHand = false): array
    {
        $hand = [];
        foreach($this->hand->toArray() as $card) {
            $hand[] = $card->normalize();
        }
        return array_merge([
            'uid'=> $this->uid
        ], $showHand ? ['hand'=>$hand] : []);
    }

    /**
     * @return Collection<int, Card>
     */
    public function getHand(): Collection
    {
        return $this->hand;
    }

    public function addHand(Card $hand): static
    {
        if (!$this->hand->contains($hand)) {
            $this->hand->add($hand);
            $hand->setGame($this);
        }

        return $this;
    }

    public function removeHand(Card $hand): static
    {
        if ($this->hand->removeElement($hand)) {
            // set the owning side to null (unless already changed)
            if ($hand->getGame() === $this) {
                $hand->setGame(null);
            }
        }

        return $this;
    }

    public function setHand($hand): static
    {
        if(is_array($hand)) {
            $this->hand = new ArrayCollection();
            array_map(fn($card) => $this->addHand($card), $hand);
        } else {
            $this->hand = $hand;
        }
        return $this;
    }

    public function sort(): static
    {
        $hand = $this->getHand()->toArray();
        usort($hand, function($a, $b) {
            if($a->getValue() == $b->getValue()) {
                return 0;
            }
            return $a->getValue() < $b->getValue() ? -1 : 1;
        });
        usort($hand, function($a, $b) {
            if($a->getColor() == $b->getColor()) {
                return 0;
            }
            return $a->getColor() < $b->getColor() ? -1 : 1;
        });
        $this->setHand($hand);

        return $this;
    }

}
