<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

final class Change
{
    /**
     * @var string
     */
    public const UNKNOWN_PACKAGE = 'Unknown Package';

    /**
     * @var string
     */
    public const UNKNOWN_CATEGORY = 'Unknown Category';

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $package;

    /**
     * @var string
     */
    private $messageWithoutPackage;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $tag;

    public function __construct(
        string $message,
        string $category,
        string $package,
        string $messageWithoutPackage,
        string $author,
        string $tag
    ) {
        $this->message = $message;
        $this->category = $category;
        $this->package = $package;
        $this->messageWithoutPackage = $messageWithoutPackage;
        $this->author = $author;
        $this->tag = $tag;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getMessageWithoutPackage(): string
    {
        return $this->messageWithoutPackage;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
