<?php

namespace App\Components;

use App\Entity\BlogPost;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('blog_post')]
class BlogPostComponent
{
    public BlogPost $blogPost;
    public bool $enableEdit = false;
}