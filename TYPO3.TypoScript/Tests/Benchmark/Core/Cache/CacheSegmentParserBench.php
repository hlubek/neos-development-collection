<?php
namespace TYPO3\TypoScript\Tests\Benchmark\Core\Cache;

/**
 * @BeforeMethods({"setUp"})
 */
class CacheSegmentParserBench {

    /**
     * @var string
     */
    protected $content;

    /**
     * @var \TYPO3\TypoScript\Core\Cache\CacheSegmentParser
     */
    protected $currentParser;

    public function setUp() {
        $this->content = str_repeat(file_get_contents(__DIR__ . '/RenderedFixture.html'), 10);
    }

    /**
     * @Revs(100)
     */
    public function benchCurrent() {

        $this->currentParser = new \TYPO3\TypoScript\Core\Cache\CacheSegmentParser();
        $this->currentParser->extractRenderedSegments($this->content, '57fff3e10fcc3');
    }

    /**
     * @Revs(100)
     */
    public function benchRefactoredStrpos() {
        $parser = new \TYPO3\TypoScript\Core\Cache\CacheSegmentParserRefactoredStrpos($this->content, '57fff3e10fcc3');
    }

    /**
     * @Revs(100)
     */
    public function benchRefactoredStrposInlined() {
        $parser = new \TYPO3\TypoScript\Core\Cache\CacheSegmentParserRefactoredStrposInlined($this->content, '57fff3e10fcc3');
    }

}