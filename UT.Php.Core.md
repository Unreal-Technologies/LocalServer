# UT.Php.Core
Version 1.0.0.0
## UT_Php_Core
```php
class Version
{
	public function build(): int
	public function patch(): int
	public function minor(): int
	public function major(): int
	public function update(string $version): void
	public function increment(): void
	public static function parse(string $value, array $subVersions = []): ?Version
	public function __construct(int $major, int $minor, int $patch, int $build, array $subVersions = [])
	public function __toString(): string
}

```
## UT_Php_Core\Collections
```php
class Dictionary implements \UT_Php_Core\Interfaces\IDictionary
{
	protected array $buffer = []
	public static function fromArray(array $kvp): Dictionary
	public function add(mixed $key, mixed $value, bool $setAsArray = false): bool
	public function get(mixed $key): mixed
	public function remove(mixed $key): bool
	public function toArray(): array
	public function keys(): array
	public function values(): array
}

```
```php
class Linq implements \UT_Php_Core\Interfaces\ILinq
{
	public function __construct(array $collection)
	public function skip(int $count): \UT_Php_Core\Interfaces\ILinq
	public function where(\Closure $lambda): \UT_Php_Core\Interfaces\ILinq
	public function select(\Closure $lambda): \UT_Php_Core\Interfaces\ILinq
	public function groupBy(\Closure $lambda): \UT_Php_Core\Interfaces\ILinq
	public function toArray(\Closure $lambda = null, bool $keepKeys = false): array
	public function firstOrDefault(\Closure $lambda = null): mixed
	public function count(): int
	public function sum(\Closure $lambda = null): \UT_Php_Core\Interfaces\ILinq
	public function avg(\Closure $lambda = null): \UT_Php_Core\Interfaces\ILinq
	public function orderBy(\Closure $lambda = null, \UT_Php_Core\Enums\SortDirections $direction = \UT_Php_Core\Enums\SortDirections::Asc): \UT_Php_Core\Interfaces\ILinq
}

```
## UT_Php_Core\Collections\Generic
```php
class WindowsDataParser extends \UT_Php_Core\Collections\Dictionary
{
	public function __construct(string $header, string $data, ?string $indentations = null)
}

```
## UT_Php_Core\Drawing
```php
class Color implements \UT_Php_Core\Interfaces\IColor
{
	public static function fromRGB(int $r, int $g, int $b): Color
	public static function fromRGBA(int $r, int $g, int $b, int $a): Color
	public function r(): int
	public function g(): int
	public function b(): int
	public function a(): int
}

```
```php
abstract class Image extends \UT_Php_Core\IO\File implements \UT_Php_Core\Interfaces\IImage
{
	public static function getImage(\UT_Php_Core\Interfaces\IFile $file): ?\UT_Php_Core\Interfaces\IImage
	abstract public function imageCreate(): mixed
	abstract public function imageSave(\GdImage $image): bool
	public function gdDrawRectangle(\UT_Php_Core\Interfaces\IRectangle $rectangle, \UT_Php_Core\Interfaces\IColor $fillColor, \UT_Php_Core\Interfaces\IColor $borderColor = null): void
	public function gdDrawEllipse(\UT_Php_Core\Interfaces\IRectangle $rectangle, \UT_Php_Core\Interfaces\IColor $fillColor, \UT_Php_Core\Interfaces\IColor $borderColor = null): void
	public function size(): ?\UT_Php_Core\Interfaces\ISize2D
	public function gdOpen(): bool
	public function gdSaveAs(\UT_Php_Core\IO\File $file): bool
}

```
```php
class Point2D implements \UT_Php_Core\Interfaces\IPoint2D
{
	public function __construct(float $x, float $y)
	public function x(): float
	public function y(): float
}

```
```php
class Point3D extends Point2D implements \UT_Php_Core\Interfaces\IPoint3D
{
	public function __construct(float $x, float $y, float $z)
	public function z(): float
}

```
```php
class Rectangle implements \UT_Php_Core\Interfaces\IRectangle
{
	public function __construct(\UT_Php_Core\Interfaces\IPoint2D $size, \UT_Php_Core\Interfaces\ISize2D $location, int $rotation = 0)
	public function location(): \UT_Php_Core\Interfaces\IPoint2D
	public function size(): \UT_Php_Core\Interfaces\ISize2D
	public function rotation(): int
}

```
```php
class Size2D implements \UT_Php_Core\Interfaces\ISize2D
{
	public function __construct(float $w, float $h)
	public function w(): float
	public function h(): float
}

```
## UT_Php_Core\Enums
```php
enum RequestMethods
{
	case Get
	case Head
	case Post
	case Put
	case Delete
	case Connect
	case Options
	case Trace
	case Patch
}

```
```php
enum SortDirections
{
	case Asc
	case Desc
}

```
## UT_Php_Core\Exceptions
```php
class ArgumentException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
}

```
```php
class NotImplementedException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
}

```
```php
class UndefinedException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
}

```
## UT_Php_Core\Html
```php
class Directory
{
	public function __construct(\UT_Php_Core\Interfaces\IDirectory $directory, \UT_Php_Core\Interfaces\IDirectory $root, int $offset = 0)
	public function __toString(): string
}

```
```php
abstract class PageController
{
	abstract public function initialize(): void
	abstract public function setup(string &$title, array &$css): void
	abstract public function render(): string
	final protected function root(): \UT_Php_Core\Interfaces\IDirectory
	final public function __construct(\UT_Php_Core\Interfaces\IRouter $router)
	final public function __toString(): string
}

```
## UT_Php_Core\IO
```php
class Directory implements \UT_Php_Core\Interfaces\IDirectory
{
	public static function fromString(string $dir): Directory
	public function remove(): bool
	public static function fromDirectory(\UT_Php_Core\Interfaces\IDirectory $dir, string $name): ?Directory
	public function parent(): Directory
	public function contains(string $regex): bool
	public function name(): string
	public function path(): string
	public function exists(): bool
	public function create(): bool
	public function list(string $regex = null, bool $refresh = false): array
	public function read(?string &$out): bool
	public function open(): bool
	public function close(): void
}

```
```php
class File implements \UT_Php_Core\Interfaces\IFile
{
	public static function fromString(string $path): File
	public function remove(): bool
	public function relativeTo(\UT_Php_Core\Interfaces\IDirectory $dir): ?string
	public function copyTo(\UT_Php_Core\Interfaces\IDirectory $dir, string $name = null): bool
	public function write(string $stream): void
	public function parent(): ?\UT_Php_Core\Interfaces\IDirectory
	public function content(): string
	public static function fromDirectory(\UT_Php_Core\Interfaces\IDirectory $dir, string $name): ?File
	public function extension(): string
	public function basename(): string
	public function name(): string
	public function contains(string $regex): bool
	public function exists(): bool
	public function path(): string
	protected function __construct(string $path)
}

```
```php
class Memory
{
	public static function parse(string $memory): Memory
	public static function fromInt(int $value): Memory
	public function format(int $decimals = 2): string
	public function value(): int
}

```
```php
class Process
{
	public static function list(): array
	public function sessionId(): int
	public function sessionName(): string
	public function pidInfo(int $pid): \UT_Php_Core\Collections\Dictionary
	public function pidCount(): int
	public function pidList(): array
	public function totalMemory(bool $format = false): mixed
	public function pidMemory(int $pid, bool $format = false): mixed
	public function name(): string
}

```
```php
class Server
{
	public static function ram(): array
}

```
## UT_Php_Core\IO\Common
```php
class Bmp extends \UT_Php_Core\Drawing\Image implements \UT_Php_Core\Interfaces\IBmpFile
{
	public function imageCreate(): mixed
	public function imageSave(\GdImage $image): bool
}

```
```php
class Dtd extends \UT_Php_Core\IO\File implements \UT_Php_Core\Interfaces\IDtdFile
{
	public function __construct(string $path, bool $requiresExtension = true)
	public function systemId(): ?string
}

```
```php
class Png extends \UT_Php_Core\Drawing\Image implements \UT_Php_Core\Interfaces\IPngFile
{
	public function imageCreate(): mixed
	public function imageSave(\GdImage $image): bool
}

```
```php
class Xml extends \UT_Php_Core\IO\File implements \UT_Php_Core\Interfaces\IXmlFile
{
	public function __construct(string $path, bool $requiresExtension = true)
	public function document(): ?\UT_Php_Core\Interfaces\IXmlDocument
}

```
## UT_Php_Core\IO\Xml
```php
final class Doctype implements \UT_Php_Core\Interfaces\IXmlDoctype
{
	public function __construct(string $start, string $end, array $attributes)
	public function __toString(): string
	public function attributes(): array
	public static function xml(string $version = '1.0', string $encoding = 'utf-8', bool $standalone = true): Doctype
	public static function html5(): Doctype
	public static function htmlStrict(): Doctype
	public static function htmlTransitional(): Doctype
	public static function htmlFrameset(): Doctype
	public static function xHtmlStrict(): Doctype
	public static function xHtmlTransitional(): Doctype
	public static function xHtmlFrameset(): Doctype
	public static function xHtml(): Doctype
}

```
```php
final class Document extends Element implements \UT_Php_Core\Interfaces\IXmlDocument
{
	public function __construct(string $name, \UT_Php_Core\Interfaces\IXmlDoctype $doctype = null)
	public function __toString(): string
	final public function asElement(): \UT_Php_Core\Interfaces\IXmlElement
	final public function doctype(): \UT_Php_Core\Interfaces\IXmlDoctype
	final public function closed(bool $value = null): ?bool
	final public function validateDtdStream(string $stream, string $root, bool $output = true, string $encoding = 'utf-8'): bool
	final public function validateXsdStream(string $stream, bool $output = true): bool
	final public function validateXsd(\UT_Php_Core\Interfaces\IFile $xsdSchemaFile, bool $output = true): bool
	final public function validateDtd(\UT_Php_Core\Interfaces\IDtdFile $dtdSchemaFile, string $root, bool $output = true, string $encoding = 'utf-8'): bool
}

```
```php
class Element implements \UT_Php_Core\Interfaces\IXmlElement
{
	public function __construct(string $name)
	public function children(): array
	public function __clone(): void
	public function remove(\UT_Php_Core\Interfaces\IXmlElement $element): bool
	public function __toString(): string
	final public static function createFromFile(\UT_Php_Core\IO\Common\Xml $file, Doctype $doctype = null): Element
	final public static function createFromXml(string $xmlstring, Doctype $doctype = null): Element
	final public static function createFromSimpleXml(\SimpleXMLElement $element, Doctype $doctype = null): Element
	final public function attributes(array $list = null): array
	final public function parent(?string $value = null): string
	final public function id(): string
	final public function text(string $text = null): ?string
	final public function name(): string
	final public function createChild(string $name): ?\UT_Php_Core\Interfaces\IXmlElement
	final public function addChild(\UT_Php_Core\Interfaces\IXmlElement $element): bool
	final public function asDocument(\UT_Php_Core\Interfaces\IXmlDoctype $doctype = null): \UT_Php_Core\Interfaces\IXmlDocument
	final public function search(string $regex, int $returnIndex = null, string $type = self::SEARCH_NAME, $recursive = true, $recursivePos = 0): ?array
	final public function updatePosition(int $pos): void
}

```
## UT_Php_Core\Interfaces
```php
interface IBmpFile extends IImage
{
}

```
```php
interface IColor
{
	public function r(): int
	public function g(): int
	public function b(): int
	public function a(): int
}

```
```php
interface IDictionary
{
	public function add(mixed $key, mixed $value, bool $setAsArray = false): bool
	public function get(mixed $key): mixed
	public function remove(mixed $key): bool
	public function toArray(): array
	public function keys(): array
	public function values(): array
}

```
```php
interface IDirectory extends IDiskManager
{
	public function create(): bool
	public function list(string $regex = null, bool $refresh = false): array
	public function read(?string &$out): bool
	public function open(): bool
	public function close(): void
	public function parent(): \UT_Php_Core\IO\Directory
}

```
```php
interface IDiskManager
{
	public function path(): string
	public function exists(): bool
	public function contains(string $data): bool
	public function name(): string
	public function remove(): bool
}

```
```php
interface IDtdFile extends IFile
{
	public function systemId(): ?string
}

```
```php
interface IFile extends IDiskManager
{
	public function relativeTo(IDirectory $dir): ?string
	public function copyTo(IDirectory $dir, string $name = null): bool
	public function parent(): ?IDirectory
	public function extension(): string
	public function basename(): string
	public function contains(string $regex): bool
}

```
```php
interface IImage extends IFile
{
	public function imageCreate(): mixed
	public function imageSave(\GdImage $image): bool
	public function gdDrawRectangle(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void
	public function gdDrawEllipse(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void
	public function size(): ?ISize2D
	public function gdOpen(): bool
	public function gdSaveAs(\UT_Php_Core\IO\File $file): bool
}

```
```php
interface ILinq
{
	public function where(\Closure $lambda): ILinq
	public function select(\Closure $lambda): ILinq
	public function groupBy(\Closure $lambda): ILinq
	public function toArray(\Closure $lambda = null): array
	public function firstOrDefault(\Closure $lambda = null): mixed
	public function count(): int
	public function sum(\Closure $lambda = null): ILinq
	public function avg(\Closure $lambda = null): ILinq
	public function skip(int $count): \UT_Php_Core\Interfaces\ILinq
	public function orderBy(\Closure $lambda = null, \UT_Php_Core\Enums\SortDirections $direction = \UT_Php_Core\Enums\SortDirections::Asc): ILinq
}

```
```php
interface IPngFile extends IImage
{
}

```
```php
interface IPoint2D
{
	public function x(): float
	public function y(): float
}

```
```php
interface IPoint3D extends IPoint2D
{
	public function z(): float
}

```
```php
interface IRectangle
{
	public function location(): IPoint2D
	public function size(): ISize2D
	public function rotation(): int
}

```
```php
interface IRouter
{
	public function add(\UT_Php_Core\Enums\RequestMethods $method, string $url, \Closure $target): void
	public function match(): void
	public function root(): \UT_Php_Core\Interfaces\IDirectory
}

```
```php
interface ISize2D
{
	public function w(): float
	public function h(): float
}

```
```php
interface IXmlDoctype
{
	public function __toString(): string
	public function attributes(): array
}

```
```php
interface IXmlDocument extends IXmlElement
{
	public function __toString(): string
	public function asElement(): IXmlElement
	public function doctype(): IXmlDoctype
	public function closed(bool $value = null): ?bool
	public function validateDtdStream(string $stream, string $root, bool $output = true, string $encoding = 'utf-8'): bool
	public function validateXsdStream(string $stream, bool $output = true): bool
	public function validateXsd(IFile $xsdSchemaFile, bool $output = true): bool
	public function validateDtd(IDtdFile $dtdSchemaFile, string $root, bool $output = true, string $encoding = 'utf-8'): bool
}

```
```php
interface IXmlElement
{
	public const SEARCH_NAME = 'name'
	public const SEARCH_ID = 'id'
	public const SEARCH_TEXT = 'text'
	public const SEARCH_POSITION = 'position'
	public const SEARCH_PARENT = 'parent'
	public const SEARCH_ATTRIBUTES = 'attributes'
	public function children(): array
	public function __clone(): void
	public function remove(IXmlElement $element): bool
	public function __toString(): string
	public function attributes(array $list = null): array
	public function parent(?string $value = null): string
	public function id(): string
	public function text(string $text = null): ?string
	public function name(): string
	public function createChild(string $name): ?IXmlElement
	public function addChild(IXmlElement $element): bool
	public function asDocument(IXmlDoctype $doctype = null): IXmlDocument
	public function search(string $regex, int $returnIndex = null, string $type = self::SEARCH_NAME, $recursive = true, $recursivePos = 0): ?array
	public function updatePosition(int $pos): void
}

```
```php
interface IXmlFile extends IFile
{
	public function document(): ?IXmlDocument
}

```
## UT_Php_Core\Routing
```php
class Router implements \UT_Php_Core\Interfaces\IRouter
{
	public function __construct(\UT_Php_Core\Interfaces\IDirectory $root, bool $caseInsensitive = false)
	public function root(): \UT_Php_Core\Interfaces\IDirectory
	public function add(\UT_Php_Core\Enums\RequestMethods $method, string $url, \Closure $target): void
	public function match(): void
}

```
