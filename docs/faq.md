# MediaFoundation FAQ

## What is MediaFoundation?

MediaFoundation is the shared BASE3 foundation for media payloads and media-related service contracts. It defines stable interfaces and small model classes for binary media, images, audio, video, documents, vector media, image rendering, image edit descriptions, and symbolic code generation.

The component is deliberately backend-neutral. It does not select an image library, video engine, audio library, document parser, barcode library, storage backend, or external service.

## Is MediaFoundation a complete media processing engine?

No. MediaFoundation provides contracts, media models, and a renderer-neutral image edit description. Concrete processing belongs to implementations of the relevant interfaces.

For example, `IImageRenderer` defines how an edited image derivative is requested, but MediaFoundation does not contain the renderer that performs decoding, resizing, color processing, watermarking, or encoding.

## What is the base media contract?

`MediaFoundation\Api\IMedia` is the common contract for media objects. It provides:

- `getMimeType()` for the media MIME type
- `getSize()` for the payload size in bytes
- `getData()` for the raw media data

The current model classes keep the payload in memory as a PHP string.

## What is `IBinaryMedia`?

`IBinaryMedia` is the generic file-like media contract. It extends `IMedia` without adding further methods.

It is useful when a caller needs to exchange arbitrary binary content without requiring type-specific properties.

`BinaryMedia` is the supplied in-memory implementation.

## Which concrete media models are included?

The package currently provides:

- `BinaryMedia` for generic binary content
- `ImageMedia` for bitmap images
- `AudioMedia` for audio payloads
- `VideoMedia` for video payloads
- `DocumentMedia` for documents
- `VectorMedia` for vector media

These are small data carriers. They do not decode, validate, transform, store, or transmit their content.

## What metadata does `ImageMedia` expose?

`ImageMedia` adds:

- width in pixels
- height in pixels
- a format string such as `png` or `jpg`

The object does not derive these values from the binary data. They are supplied when the object is created.

## What metadata does `AudioMedia` expose?

`AudioMedia` adds:

- duration in seconds
- bitrate in kbps

The API treats these values as supplied metadata. The class does not inspect the encoded audio stream itself.

## What metadata does `VideoMedia` expose?

`VideoMedia` adds:

- duration in seconds
- width in pixels
- height in pixels

The model does not decode frames or derive the metadata from the video payload.

## What metadata does `DocumentMedia` expose?

`DocumentMedia` adds:

- a document format string such as `pdf`, `docx`, `xlsx`, or `csv`
- an optional page count

A page count can be `null` when it is unknown or not meaningful for the format.

## How is vector media represented?

`IVectorMedia` and `VectorMedia` expose:

- MIME type
- raw data
- payload size
- vector format
- optional XML text

The optional XML representation is useful for XML-based formats such as SVG. MediaFoundation does not sanitize or render that XML.

## What is `IImageRenderer`?

`IImageRenderer` is the backend contract for rendering an edited image derivative.

Its method receives:

- an absolute local input path
- an `ImageEdit` description
- an absolute output path

The contract requires the renderer to treat the input as immutable and write the result to the output path. A renderer can use any suitable backend, such as a native library or command-line image engine.

MediaFoundation itself does not provide such a renderer.

## What is `ImageEdit`?

`MediaFoundation\Builder\ImageEdit` is a fluent, renderer-neutral description of image editing intent.

It does not edit pixels itself. It builds a structured specification that an `IImageRenderer` implementation can interpret.

The edit description supports transformation, color and tone adjustments, watermark settings, resizing, export settings, and preset markers.

## Which image transformations can `ImageEdit` describe?

The current builder supports:

- pixel-based crop
- normalized crop
- rotation
- horizontal flip
- vertical flip
- fit resizing
- fill resizing

Normalized crop coordinates are useful when a UI works with a scaled preview rather than the original pixel dimensions.

## Which image adjustments can be described?

The edit specification currently contains controls for:

- brightness
- contrast
- saturation
- vibrance
- gamma
- exposure
- highlights
- shadows
- whites
- blacks
- temperature
- tint
- clarity
- sharpness
- dehaze
- luminance noise reduction
- color noise reduction
- a renderer-specific mood hint

These values express intent. A concrete renderer decides how to map them to its processing backend and may clamp unsupported ranges.

## How are watermarks described?

`ImageEdit` can enable a watermark and describe:

- watermark file path
- opacity
- scale
- x and y offsets
- anchor position

It can also disable the watermark and restore the default watermark specification.

The foundation does not read the watermark file itself. That is the renderer's responsibility.

## Which output formats are represented by the image edit builder?

The builder has convenience methods for:

- JPEG
- WebP
- PNG

It can also describe quality, progressive JPEG behavior, metadata stripping, and sRGB output intent.

Support for a particular option depends on the active renderer.

## Does MediaFoundation remove EXIF or IPTC metadata automatically?

No processing happens automatically in MediaFoundation.

The `ImageEdit` export specification defaults `strip` to `true`, and the JPEG, WebP, and PNG helper methods can keep that setting enabled. This is a request to the renderer. Whether metadata is actually removed depends on the concrete `IImageRenderer` implementation.

Consumers must not assume that metadata was stripped without verifying the renderer behavior.

## Can an image edit description be persisted?

Yes. `ImageEdit` exposes `toArray()` and implements `JsonSerializable`.

The resulting specification can be stored by a calling component in JSON, a database, a sidecar file, or another persistence mechanism.

MediaFoundation itself does not persist the specification.

## What does `renderWith()` do?

`ImageEdit::renderWith()` is a small delegation helper. It forwards the input path, edit object, and output path to the supplied `IImageRenderer`.

It does not select, discover, or create a renderer and does not introduce a fallback rendering path.

## What is `ISymbolCodeService`?

`ISymbolCodeService` is a contract for generating symbolic codes from a string value.

Possible implementations can support QR codes, barcodes, or other code types. The concrete options are implementation-specific.

The result is returned as `IBinaryMedia`.

MediaFoundation does not include a concrete symbol-code generator.

## Does MediaFoundation validate the contents of media files?

No general content validation is provided by the media model classes.

The classes store the raw payload and metadata supplied by the caller. A consumer or processing implementation that accepts untrusted media must perform its own format validation, decoding checks, size limits, and security controls.

## Does MediaFoundation store files?

No. There is no media repository, file storage backend, upload directory, database schema, or persistence service in this component.

The media model objects hold data only for the lifetime of the PHP object unless another component explicitly persists it.

## Does MediaFoundation perform network requests?

No. The component contains no HTTP client, remote provider, cloud storage adapter, or other network transport.

A concrete implementation of one of its contracts could use network services, but that behavior is outside MediaFoundation itself.

## Does MediaFoundation log media data?

No logger is registered or used by this component. It does not automatically log media payloads, edit specifications, paths, or symbolic-code values.

Concrete renderers and consumers may have their own logging behavior.

## Does MediaFoundation provide authentication or authorization?

No. It does not manage users, sessions, roles, permissions, or media access policies.

Applications that expose media to users must enforce access at their own application or storage boundary.

## Does MediaFoundation define retention or deletion rules?

No. The component does not own persistent media storage and therefore does not define retention periods.

Retention, deletion, backups, and lifecycle management belong to the component that stores the media or edit specification.

## Does MediaFoundation depend on a specific media library?

No. Avoiding a hard dependency on a specific rendering or media-processing engine is a core purpose of the foundation.

Implementations can choose the backend appropriate to a project while consumers continue to depend on the shared contracts.

## What does the plugin class register?

`MediaFoundationPlugin` registers only its own plugin instance under the technical name `mediafoundationplugin` in the BASE3 container.

It does not bind a default image renderer or symbol-code service and does not start media processing during plugin initialization.
