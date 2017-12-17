<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private const ID_PATTERN = '\#(?<id>[0-9]+)';

    /**
     * @var string
     */
    private const UNLINKED_ID_PATTERN = '#\[' . self::ID_PATTERN . '\]\s+#';

    /**
     * @var string
     */
    private const LINKED_ID_PATTERN = '#\[' . self::ID_PATTERN . '\]:\s+#';

    /**
     * @var string[]
     */
    private $linkedIds = [];

    /**
     * @var resource
     */
    private $curl;

    public function __construct()
    {
        $this->curl = $this->createCurl();
    }

    public function processContent(string $content, string $repositoryLink): string
    {
        $this->resolveLinkedElements($content);

        $linksToAppend = [];

        $matches = Strings::matchAll($content, self::UNLINKED_ID_PATTERN);
        foreach ($matches as $match) {
            if (array_key_exists($match['id'], $linksToAppend)) {
                continue;
            }

            if (in_array($match['id'], $this->linkedIds, true)) {
                continue;
            }

            $possibleUrls = [
                $repositoryLink . '/pull/' . $match['id'],
                $repositoryLink . '/issues/' . $match['id'],
            ];

            foreach ($possibleUrls as $possibleUrl) {
                if ($this->doesUrlExist($possibleUrl)) {
                    $markdownLink = sprintf(
                        '[#%d]: %s',
                        $match['id'],
                        $possibleUrl
                    );

                    $linksToAppend[$match['id']] = $markdownLink;
                }
            }
        }

        if (! count($linksToAppend)) {
            return $content;
        }

        rsort($linksToAppend);

        // append new links to the file
        return $content . PHP_EOL . implode(PHP_EOL, $linksToAppend);
    }

    private function doesUrlExist(string $url): bool
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_exec($this->curl);

        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE) === 200;
    }

    private function resolveLinkedElements(string $content): void
    {
        $matches = Strings::matchAll($content, self::LINKED_ID_PATTERN);
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }
    }

    /**
     * @return resource
     */
    private function createCurl()
    {
        $curl = curl_init();

        // set to HEAD request
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        // don't output the response
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return $curl;
    }
}
