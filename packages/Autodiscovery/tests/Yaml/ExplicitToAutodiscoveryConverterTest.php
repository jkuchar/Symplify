<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;
use Symplify\Autodiscovery\Tests\AbstractContainerAwareTestCase;
use Symplify\Autodiscovery\Yaml\ExplicitToAutodiscoveryConverter;

final class ExplicitToAutodiscoveryConverterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const SPLIT_PATTERN = "#---\n#";

    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    protected function setUp(): void
    {
        $this->explicitToAutodiscoveryConverter = $this->container->get(ExplicitToAutodiscoveryConverter::class);
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixture/short_tags.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/vendor.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/singly_implemented_interfaces.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/singly_implemented_interfaces_excluded.yaml', 2, true);
        $this->doTestFile(__DIR__ . '/Fixture/first.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/tags_with_values.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/shopsys.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/elasticr.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/untouch.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/existing_autodiscovery.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/blog_post_votruba.yaml', 1);
        $this->doTestFile(__DIR__ . '/Fixture/exclude.yaml', 4);
    }

    private function doTestFile(string $file, int $nestingLevel, bool $removeSinglyImplemented = false): void
    {
        $yamlContent = FileSystem::read($file);

        [$originalYamlContent, $expectedYamlContent] = $this->splitFile($yamlContent);

        $originalYaml = Yaml::parse($originalYamlContent);
        $expectedYaml = Yaml::parse($expectedYamlContent);

        $this->assertSame(
            $expectedYaml,
            $this->explicitToAutodiscoveryConverter->convert(
                $originalYaml,
                $file,
                $nestingLevel,
                $removeSinglyImplemented
            ),
            'Caused by ' . $file
        );
    }

    /**
     * @return string[]
     */
    private function splitFile(string $yamlContent): array
    {
        if (Strings::match($yamlContent, self::SPLIT_PATTERN)) {
            return Strings::split($yamlContent, self::SPLIT_PATTERN);
        }

        return [$yamlContent, $yamlContent];
    }
}
