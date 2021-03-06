<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Exception\Renderable\File\UnsupportedMethodException;
use Symplify\Statie\Renderable\File\PostFile;
use function Safe\sprintf;

final class GeneratorExceptionsTest extends AbstractGeneratorTest
{
    public function testPostExceptionsOnUnset(): void
    {
        $post = $this->getPost();
        $this->expectException(UnsupportedMethodException::class);
        unset($post['key']);
    }

    public function testPostExceptionOnSet(): void
    {
        $post = $this->getPost();
        $this->expectException(UnsupportedMethodException::class);
        $post['key'] = 'value';
    }

    public function testPostExceptionOnGetNonExistingSuggestion(): void
    {
        $post = $this->getPost();

        $this->expectException(AccessKeyNotAvailableException::class);
        $this->expectExceptionMessage(
            sprintf('Value "tite" was not found for "%s" object. Did you mean "title"?', PostFile::class)
        );

        $post['tite'];
    }

    public function testPostExceptionOnGetNonExistingAllKeys(): void
    {
        $post = $this->getPost();

        $this->expectException(AccessKeyNotAvailableException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Value "key" was not found for "%s" object. Available keys are: "id", "title", "relativeUrl".',
                PostFile::class
            )
        );

        $post['key'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/GeneratorSource/statie.yml';
    }

    private function getPost(): PostFile
    {
        $this->generator->run();

        $posts = $this->statieConfiguration->getOption('posts');

        return $posts[4];
    }
}
