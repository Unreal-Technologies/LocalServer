# UT.Php.Core
Version 1.0.0.1
## UT_Php_Core
```php
class Version
{
	public function __construct(int $major, int $minor, int $patch, int $build, array $subVersions = []);
	public function __toString(): string;
	public function build(): int;
	public function increment(): void;
	public function major(): int;
	public function minor(): int;
	public function patch(): int;
	public function update(string $version): void;
	public static function parse(string $value, array $subVersions = []): ?Version;
}

```
## UT_Php_Core\Collections
```php
class Dictionary implements IDictionary
{
	protected array $buffer = [];

	public function add(mixed $key, mixed $value, bool $setAsArray = false): bool;
	public function get(mixed $key): mixed;
	public function keys(): array;
	public function remove(mixed $key): bool;
	public function toArray(): array;
	public function values(): array;
	public static function fromArray(array $kvp): Dictionary;
}

```
```php
interface IDictionary
{
	public function add(mixed $key, mixed $value, bool $setAsArray = false): bool;
	public function get(mixed $key): mixed;
	public function keys(): array;
	public function remove(mixed $key): bool;
	public function toArray(): array;
	public function values(): array;
}

```
```php
interface ILinq
{
	public function avg(\Closure $lambda = null): ILinq;
	public function count(): int;
	public function firstOrDefault(\Closure $lambda = null): mixed;
	public function groupBy(\Closure $lambda): ILinq;
	public function orderBy(\Closure $lambda = null, SortDirections $direction = SortDirections::Asc): ILinq;
	public function select(\Closure $lambda): ILinq;
	public function skip(int $count): ILinq;
	public function sum(\Closure $lambda = null): ILinq;
	public function toArray(\Closure $lambda = null): array;
	public function where(\Closure $lambda): ILinq;
}

```
```php
class Linq implements ILinq
{
	public function __construct(array $collection);
	public function avg(\Closure $lambda = null): ILinq;
	public function count(): int;
	public function firstOrDefault(\Closure $lambda = null): mixed;
	public function groupBy(\Closure $lambda): Linq;
	public function orderBy(\Closure $lambda = null, SortDirections $direction = SortDirections::Asc): Linq;
	public function select(\Closure $lambda): Linq;
	public function skip(int $count): ILinq;
	public function sum(\Closure $lambda = null): ILinq;
	public function toArray(\Closure $lambda = null, bool $keepKeys = false): array;
	public function where(\Closure $lambda): Linq;
}

```
```php
enum SortDirections
{
	case Asc;
	case Desc;
}

```
## UT_Php_Core\Collections\Generic
```php
class WindowsDataParser extends \UT_Php_Core\Collections\Dictionary
{
	public function __construct(string $header, string $data, ?string $indentations = null);
}

```
## UT_Php_Core\Drawing
```php
class Color implements IColor
{
	public function a(): int;
	public function b(): int;
	public function g(): int;
	public function r(): int;
	public static function fromRGB(int $r, int $g, int $b): Color;
	public static function fromRGBA(int $r, int $g, int $b, int $a): Color;
}

```
```php
interface IColor
{
	public function a(): int;
	public function b(): int;
	public function g(): int;
	public function r(): int;
}

```
```php
interface IImage extends \UT_Php_Core\IO\IFile
{
	public function gdDrawEllipse(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void;
	public function gdDrawRectangle(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void;
	public function gdOpen(): bool;
	public function gdSaveAs(\UT_Php_Core\IO\File $file): bool;
	public function imageCreate(): mixed;
	public function imageSave(\GdImage $image): bool;
	public function size(): ?ISize2D;
}

```
```php
abstract class Image extends \UT_Php_Core\IO\File implements IImage
{
	abstract public function imageCreate(): mixed;
	public function gdDrawEllipse(IRectangle $rectangle, IColor $fillColor, IColor $borderColor = null): void;
	public function gdOpen(): bool;
	public function gdSaveAs(\UT_Php_Core\IO\IFile $file): bool;
	public function size(): ?ISize2D;
	public static function getImage(\UT_Php_Core\IO\IFile $file): ?IImage;
}

```
```php
interface IPoint2D
{
	public function x(): float;
	public function y(): float;
}

```
```php
interface IPoint3D extends IPoint2D
{
	public function z(): float;
}

```
```php
interface IRectangle
{
	public function location(): IPoint2D;
	public function rotation(): int;
	public function size(): ISize2D;
}

```
```php
interface ISize2D
{
	public function h(): float;
	public function w(): float;
}

```
```php
class Point2D implements IPoint2D
{
	public function __construct(float $x, float $y);
	public function x(): float;
	public function y(): float;
}

```
```php
class Point3D extends Point2D implements IPoint3D
{
	public function __construct(float $x, float $y, float $z);
	public function z(): float;
}

```
```php
class Rectangle implements IRectangle
{
	public function __construct(IPoint2D $size, ISize2D $location, int $rotation = 0);
	public function location(): IPoint2D;
	public function rotation(): int;
	public function size(): ISize2D;
}

```
```php
class Size2D implements ISize2D
{
	public function __construct(float $w, float $h);
	public function h(): float;
	public function w(): float;
}

```
## UT_Php_Core\Exceptions
```php
class ArgumentException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null);
}

```
```php
class NotImplementedException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null);
}

```
```php
class UndefinedException extends \Exception
{
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null);
}

```
## UT_Php_Core\Html
```php
class Directory
{
	public function __construct(\UT_Php_Core\IO\IDirectory $directory, \UT_Php_Core\IO\IDirectory $root, int $offset = 0);
	public function __toString(): string;
}

```
```php
abstract class PageController
{
	abstract public function initialize(): void;
	final public function __construct(\UT_Php_Core\Routing\IRouter $router);
	final public function __toString(): string;
}

```
## UT_Php_Core\IO
```php
class Directory implements IDirectory
{
	public function close(): void;
	public function contains(string $regex): bool;
	public function create(): bool;
	public function exists(): bool;
	public function list(string $regex = null, bool $refresh = false): array;
	public function name(): string;
	public function open(): bool;
	public function parent(): Directory;
	public function path(): string;
	public function read(?string &$out): bool;
	public function remove(): bool;
	public static function fromDirectory(IDirectory $dir, string $name): ?Directory;
	public static function fromString(string $dir): Directory;
}

```
```php
class File implements IFile
{
	protected function __construct(string $path);
	public function asBmp(): ?Common\IBmpFile;
	public function asDtd(): ?Common\IDtdFile;
	public function asPhp(): ?Common\IPhpFile;
	public function asPng(): ?Common\IPngFile;
	public function asXml(): ?Common\IXmlFile;
	public function basename(): string;
	public function copyTo(IDirectory $dir, string $name = null): bool;
	public function exists(): bool;
	public function extension(): string;
	public function name(): string;
	public function parent(): ?IDirectory;
	public function path(): string;
	public function read(): string;
	public function relativeTo(IDirectory $dir): ?string;
	public function remove(): bool;
	public function write(string $stream): void;
	public static function fromDirectory(IDirectory $dir, string $name): ?IFile;
	public static function fromFile(IFile $file): IFile;
	public static function fromString(string $path): IFile;
}

```
```php
interface IDirectory extends IDiskManager
{
	public function close(): void;
	public function contains(string $regex): bool;
	public function create(): bool;
	public function list(string $regex = null, bool $refresh = false): array;
	public function open(): bool;
	public function parent(): \UT_Php_Core\IO\Directory;
	public function read(?string &$out): bool;
}

```
```php
interface IDiskManager
{
	public function exists(): bool;
	public function name(): string;
	public function path(): string;
	public function remove(): bool;
}

```
```php
interface IFile extends IDiskManager
{
	public function asBmp(): ?Common\IBmpFile;
	public function asDtd(): ?Common\IDtdFile;
	public function asPhp(): ?Common\IPhpFile;
	public function asPng(): ?Common\IPngFile;
	public function asXml(): ?Common\IXmlFile;
	public function basename(): string;
	public function copyTo(IDirectory $dir, string $name = null): bool;
	public function extension(): string;
	public function parent(): ?IDirectory;
	public function read(): string;
	public function relativeTo(IDirectory $dir): ?string;
	public function write(string $stream): void;
	public static function fromDirectory(IDirectory $dir, string $name): ?IFile;
	public static function fromFile(IFile $file): IFile;
	public static function fromString(string $path): IFile;
}

```
```php
class Memory
{
	public function format(int $decimals = 2): string;
	public function value(): int;
	public static function fromInt(int $value): Memory;
	public static function parse(string $memory): Memory;
}

```
```php
class Process
{
	public function name(): string;
	public function pidCount(): int;
	public function pidInfo(int $pid): \UT_Php_Core\Collections\Dictionary;
	public function pidList(): array;
	public function pidMemory(int $pid, bool $format = false): mixed;
	public function sessionId(): int;
	public function sessionName(): string;
	public function totalMemory(bool $format = false): mixed;
	public static function list(): array;
}

```
```php
class Server
{
	public static function ram(): array;
}

```
## UT_Php_Core\IO\Common
```php
class Bmp extends \UT_Php_Core\Drawing\Image implements IBmpFile
{
	public function imageCreate(): mixed;
	public function imageSave(\GdImage $image): bool;
}

```
```php
class Dtd extends \UT_Php_Core\IO\File implements IDtdFile
{
	public function __construct(string $path, bool $requiresExtension = true);
	public function systemId(): ?string;
}

```
```php
interface IBmpFile extends \UT_Php_Core\Drawing\IImage
{
}

```
```php
interface IDtdFile extends \UT_Php_Core\IO\IFile
{
	public function systemId(): ?string;
}

```
```php
interface IPhpFile extends \UT_Php_Core\IO\IFile
{
	public function cases(): array;
	public function compose(bool $methodContent = true, bool $singleLine = false): string;
	public function constants(): array;
	public function members(): array;
	public function methods(): array;
	public function namespace(): ?\UT_Php_Core\IO\Common\Php\TokenNamespace;
	public function object(): ?\UT_Php_Core\IO\Common\Php\TokenObject;
	public function traits(): array;
}

```
```php
interface IPngFile extends \UT_Php_Core\Drawing\IImage
{
}

```
```php
interface IXmlFile extends \UT_Php_Core\IO\IFile
{
	public function document(): ?\UT_Php_Core\IO\Xml\IXmlDocument;
}

```
```php
class Php extends \UT_Php_Core\IO\File implements IPhpFile
{
	public function __construct(string $path);
	public function cases(): array;
	public function compose(bool $methodContent = true, bool $singleLine = false): string;
	public function constants(): array;
	public function members(): array;
	public function methods(): array;
	public function namespace(): ?Php\TokenNamespace;
	public function object(): ?Php\TokenObject;
	public function tokens(): array;
	public function traits(): array;
}

```
```php
class Png extends \UT_Php_Core\Drawing\Image implements IPngFile
{
	public function imageCreate(): mixed;
	public function imageSave(\GdImage $image): bool;
}

```
```php
class Xml extends \UT_Php_Core\IO\File implements IXmlFile
{
	public function __construct(string $path, bool $requiresExtension = true);
	public function document(): ?\UT_Php_Core\IO\Xml\Document;
}

```
## UT_Php_Core\IO\Common\Php
```php
interface ICase extends IPhpParser
{
}

```
```php
interface IMember extends IPhpParser
{
	public function isPrivate(): bool;
	public function isProtected(): bool;
	public function isPublic(): bool;
	public function isStatic(): bool;
	public function replace(string $old, string $new): void;
}

```
```php
interface IMethod extends IPhpParser
{
	public function __construct(array $head, ?array $body = null);
	public function body(): ?string;
	public function isPrivate(): bool;
	public function isProtected(): bool;
	public function isPublic(): bool;
	public function replace(string $old, string $new, ReplaceTypes $type): void;
	public function strip(): void;
	public function variables(bool $includeParameters = true): array;
}

```
```php
interface INamespace extends IPhpParser
{
}

```
```php
interface IObject extends IPhpParser
{
	public function extends(): ?string;
	public function implements(): array;
	public function isAbstract(): bool;
	public function isClass(): bool;
	public function isEnum(): bool;
	public function isFinal(): bool;
	public function isInterface(): bool;
	public function isTrait(): bool;
}

```
```php
interface IPhpParser
{
	public function __construct(array $tokens, ?array $args = null);
	public function declaration(): string;
	public function name(): string;
}

```
```php
interface ITrait extends IPhpParser
{
}

```
```php
enum ReplaceTypes
{
	case Constant;
	case Declaration;
	case Member;
	case Method;
	case Variable;
}

```
```php
trait TAccess
{
	public function isPrivate(): bool;
	public function isProtected(): bool;
	public function isPublic(): bool;
}

```
```php
trait TDeclarations
{
	public function isAbstract(): bool;
	public function isFinal(): bool;
}

```
```php
class TokenCase implements ICase
{
	use TPhpParser;

	protected array $tokens;

	public function __construct(array $tokens, ?array $args = null);
}

```
```php
class TokenMember implements IMember
{
	use TAccess;
	use TDeclarations;
	use TPhpParser;

	protected array $tokens;

	public function __construct(array $tokens, ?array $args = null);
	public function isStatic(): bool;
	public function name(): string;
	public function replace(string $old, string $new): void;
}

```
```php
class TokenMethod implements IMethod
{
	use TAccess;

	protected array $tokens;

	public function __construct(array $head, ?array $body = null);
	public function body(): ?string;
	public function declaration(): string;
	public function name(): string;
	public function replace(string $old, string $new, ReplaceTypes $type): void;
	public function strip(): void;
	public function variables(bool $includeParameters = true): array;
}

```
```php
class TokenNamespace implements INamespace
{
	use TPhpParser;

	protected array $tokens;

	public function __construct(array $tokens, ?array $args = null);
	public function name(): string;
}

```
```php
class TokenObject implements IObject
{
	use TDeclarations;
	use TPhpParser;

	protected array $tokens;

	public function __construct(array $tokens, ?array $args = null);
	public function extends(): ?string;
	public function implements(): array;
	public function isClass(): bool;
	public function isEnum(): bool;
	public function isInterface(): bool;
	public function isTrait(): bool;
}

```
```php
class TokenTrait implements ITrait
{
	use TPhpParser;

	protected array $tokens;

	public function __construct(array $tokens, ?array $args = null);
}

```
```php
trait TPhpParser
{
	public function declaration(): string;
	public function name(): string;
}

```
## UT_Php_Core\IO\Xml
```php
final class Doctype implements IXmlDoctype
{
	public function __construct(string $start, string $end, array $attributes);
	public function __toString(): string;
	public function attributes(): array;
	public static function html5(): Doctype;
	public static function htmlFrameset(): Doctype;
	public static function htmlStrict(): Doctype;
	public static function htmlTransitional(): Doctype;
	public static function xHtml(): Doctype;
	public static function xHtmlFrameset(): Doctype;
	public static function xHtmlStrict(): Doctype;
	public static function xHtmlTransitional(): Doctype;
	public static function xml(string $version = '1.0', string $encoding = 'utf-8', bool $standalone = true): Doctype;
}

```
```php
final class Document extends Element implements IXmlDocument
{
	final public function asElement(): IXmlElement;
	final public function closed(bool $value = null): ?bool;
	final public function doctype(): IXmlDoctype;
	final public function validateDtd(\UT_Php_Core\IO\Common\IDtdFile $dtdSchemaFile, string $root, bool $output = true, string $encoding = 'utf-8'): bool;
	final public function validateDtdStream(string $stream, string $root, bool $output = true, string $encoding = 'utf-8'): bool;
	final public function validateXsd(\UT_Php_Core\IO\IFile $xsdSchemaFile, bool $output = true): bool;
	final public function validateXsdStream(string $stream, bool $output = true): bool;
	public function __construct(string $name, IXmlDoctype $doctype = null);
	public function __toString(): string;
}

```
```php
class Element implements IXmlElement
{
	final public function addChild(IXmlElement $element): bool;
	final public function asDocument(IXmlDoctype $doctype = null): Document;
	final public function attributes(array $list = null): array;
	final public function createChild(string $name): ?Element;
	final public function id(): string;
	final public function name(): string;
	final public function parent(?string $value = null): string;
	final public function search(string $regex, int $returnIndex = null, string $type = self::SEARCH_NAME, $recursive = true, $recursivePos = 0): ?array;
	final public function text(string $text = null): ?string;
	final public function updatePosition(int $pos): void;
	final public static function createFromFile(\UT_Php_Core\IO\Common\Xml $file, Doctype $doctype = null): Element;
	final public static function createFromSimpleXml(\SimpleXMLElement $element, Doctype $doctype = null): Element;
	final public static function createFromXml(string $xmlstring, Doctype $doctype = null): Element;
	public function __clone(): void;
	public function __construct(string $name);
	public function __toString(): string;
	public function children(): array;
	public function remove(IXmlElement $element): bool;
}

```
```php
interface IXmlDoctype
{
	public function __toString(): string;
	public function attributes(): array;
}

```
```php
interface IXmlDocument extends IXmlElement
{
	public function __toString(): string;
	public function asElement(): IXmlElement;
	public function closed(bool $value = null): ?bool;
	public function doctype(): IXmlDoctype;
	public function validateDtd(\UT_Php_Core\IO\Common\IDtdFile $dtdSchemaFile, string $root, bool $output = true, string $encoding = 'utf-8'): bool;
	public function validateDtdStream(string $stream, string $root, bool $output = true, string $encoding = 'utf-8'): bool;
	public function validateXsd(\UT_Php_Core\IO\IFile $xsdSchemaFile, bool $output = true): bool;
	public function validateXsdStream(string $stream, bool $output = true): bool;
}

```
```php
interface IXmlElement
{
	public function __clone(): void;
	public function __toString(): string;
	public function addChild(IXmlElement $element): bool;
	public function attributes(array $list = null): array;
	public function children(): array;
	public function createChild(string $name): ?IXmlElement;
	public function id(): string;
	public function name(): string;
	public function parent(?string $value = null): string;
	public function remove(IXmlElement $element): bool;
	public function search(string $regex, int $returnIndex = null, string $type = self::SEARCH_NAME, $recursive = true, $recursivePos = 0): ?array;
	public function text(string $text = null): ?string;
	public function updatePosition(int $pos): void;
}

```
## UT_Php_Core\Routing
```php
interface IRouter
{
	public function add(RequestMethods $method, string $url, \Closure $target): void;
	public function match(): void;
	public function root(): \UT_Php_Core\IO\IDirectory;
}

```
```php
enum RequestMethods
{
	case Connect;
	case Delete;
	case Get;
	case Head;
	case Options;
	case Patch;
	case Post;
	case Put;
	case Trace;
}

```
```php
class Router implements IRouter
{
	public function __construct(\UT_Php_Core\IO\IDirectory $root, bool $caseInsensitive = false);
	public function add(RequestMethods $method, string $url, \Closure $target): void;
	public function match(): void;
	public function root(): \UT_Php_Core\IO\IDirectory;
}

```
