<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;

final class ChangeSorterTest extends TestCase
{
    /**
     * @var ChangeSorter
     */
    private $changeSorter;

    protected function setUp(): void
    {
        $this->changeSorter = new ChangeSorter();
    }

    public function testSortWithCategoryPriority(): void
    {
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sort($changes, ChangeSorter::PRIORITY_CATEGORIES);
        $this->assertNotSame($changes, $sortedChanges);

        $firstChange = array_shift($sortedChanges);
        $this->assertSame('Added', $firstChange->getCategory());
        $this->assertSame('B', $firstChange->getPackage());

        $secondChange = array_shift($sortedChanges);
        $this->assertSame('Changed', $secondChange->getCategory());
        $this->assertSame('B', $secondChange->getPackage());
    }

    public function testSortWithPackagePriority(): void
    {
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sort($changes, ChangeSorter::PRIORITY_PACKAGES);
        $this->assertNotSame($changes, $sortedChanges);

        $firstChange = array_shift($sortedChanges);
        $this->assertSame('A', $firstChange->getPackage());
        $this->assertSame('Removed', $firstChange->getCategory());

        $secondChange = array_shift($sortedChanges);
        $this->assertSame('B', $secondChange->getPackage());
        $this->assertSame('Added', $secondChange->getCategory());
    }

    /**
     * @return Change[]
     */
    private function createChanges(): array
    {
        return [
            new Change('[B] message', 'Changed', 'B', 'message', 'me', 'Unreleased'),
            new Change('[B] message', 'Added', 'B', 'message', 'me', 'Unreleased'),
            new Change('[A] message', 'Removed', 'A', 'message', 'me', 'Unreleased'),
        ];
    }
}
