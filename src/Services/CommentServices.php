<?php


namespace App\Services;

use App\Repository\CommentRepository;
use DateTimeZone;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentServices
{
    // todo : to prevent  deprecated warning i must not declare properties dynamically
    // do setters
    public function __construct(
        User $user,
        string $slug,
        TrickRepository $trickRepository,
        EntityManagerInterface $em
    )
    {
        $this->user = $user;
        $this->slug = $slug;
        $this->trick = $trickRepository->findOneBy(['slug'=>$this->slug]);
        $this->comment = new Comment($this->trick);
        $this->form = new CommentFormType();
        $this->em = $em;
    }

    public function createFormView()
    {
        $this->form->createView();
    }

    public function handleForm()
    {
        $this->setCreatedAt();
        $this->setUser();

        $this->em->persist($this->comment);
        $this->em->flush();
    }

    public function SortedDescComments(CommentRepository $commentRepository)
    {
        return $commentRepository->findBy(['trick' => $this->trickRepository->findOneBy([
            'slug' => $slug,
        ])],
            ['createdAt' => 'desc']
        );
    }

    private function setCreatedAt()
    {
        $this->comment->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
    }

    private function setUser()
    {
        $this->comment->setUser($this->user);
    }
}