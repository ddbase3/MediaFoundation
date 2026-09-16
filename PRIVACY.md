# Privacy and Data Processing in MediaFoundation

This document describes the privacy-relevant behavior of the MediaFoundation component itself. MediaFoundation is a shared contract and media-model layer. It does not provide a final media storage, image renderer, upload workflow, user interface, external provider, or authorization system.

Concrete applications and implementations using these contracts can introduce additional processing that must be documented separately.

## Component scope

MediaFoundation currently provides:

- in-memory media interfaces and model classes
- image, audio, video, document, vector, and generic binary payload models
- the `IImageRenderer` contract
- the serializable `ImageEdit` description
- the `ISymbolCodeService` contract
- media-related exceptions
- a minimal BASE3 plugin registration class

The component itself does not create a database schema, write media to permanent storage, issue network requests, or register a logger.

## Media payloads can contain personal data

`IMedia::getData()` exposes the complete raw media payload as a PHP string. Depending on the caller, that payload can contain personal, confidential, or sensitive information.

Examples include:

- photographs showing identifiable people
- scanned documents
- audio recordings containing voices
- videos containing people or private environments
- office documents containing names, contact data, or business records
- images carrying metadata such as camera or location information
- vector documents containing text or embedded references

MediaFoundation does not inspect the semantic content of a payload and does not classify it as personal or non-personal.

## In-memory media models

`BinaryMedia`, `ImageMedia`, `AudioMedia`, `VideoMedia`, `DocumentMedia`, and `VectorMedia` keep their media data in object memory.

They do not persist the payload on their own. The data remains available to PHP code holding a reference to the object and is subject to normal process memory behavior.

The component does not provide secure memory wiping. Applications handling highly sensitive media should not treat object destruction as a guaranteed memory-erasure mechanism.

## Media metadata

The supplied model classes can carry metadata such as:

- MIME type
- payload size
- image width and height
- media format
- audio or video duration
- audio bitrate
- document page count
- vector XML representation

These fields are supplied by the caller. MediaFoundation does not verify that they match the binary payload.

Metadata can itself be privacy-relevant. For example, dimensions and duration can contribute to file fingerprinting, while vector XML can contain readable document content.

## Vector XML

`IVectorMedia::getXml()` can expose an XML representation for formats such as SVG.

MediaFoundation does not sanitize this XML and does not remove scripts, external references, metadata, or embedded content. Applications that render untrusted vector content in a browser or another active environment must apply appropriate sanitization and content-security controls outside this component.

## Image edit specifications

`ImageEdit` stores structured editing intent including:

- crop coordinates
- rotation and flip settings
- color and tone adjustments
- resize settings
- export format and quality
- metadata-stripping intent
- sRGB and progressive-output settings
- watermark settings
- watermark file path
- preset names

The specification can be exported with `toArray()` or `jsonSerialize()` and can therefore be persisted by a caller.

MediaFoundation does not persist it itself. If a caller stores the specification, the caller is responsible for the retention and access policy of that stored data.

## File paths

`IImageRenderer::render()` and `ImageEdit::renderWith()` use local input and output paths. Watermark settings can also include a local watermark path.

Paths can reveal server directory structure, file names, tenant identifiers, project identifiers, or other operational information. MediaFoundation does not log or expose these paths by itself, but renderer implementations and consuming applications should avoid disclosing them unnecessarily.

## Image metadata stripping

The default `ImageEdit` export specification requests metadata stripping with `strip = true`.

This is descriptive configuration only. MediaFoundation does not manipulate the file and therefore does not guarantee that EXIF, IPTC, XMP, location data, thumbnails, comments, or other embedded metadata are removed.

The active `IImageRenderer` must implement the requested behavior. Deployments relying on metadata removal for privacy should test the concrete renderer with representative source files.

## Image rendering boundary

`IImageRenderer` is a contract only. A concrete renderer receives the source path, edit description, and output path and can use native libraries, external binaries, local processes, or other mechanisms.

MediaFoundation does not determine:

- which renderer is active
- which codec libraries are used
- whether temporary files are created
- whether metadata is preserved
- whether processing leaves the local system
- how renderer errors are logged
- when output files are deleted

Those properties belong to the renderer implementation and its runtime environment.

## Symbol-code payloads

`ISymbolCodeService::generate()` accepts an arbitrary string value and implementation-specific options.

A symbolic-code value can contain personal or confidential data, such as an identifier, URL, access token, account reference, contact information, or business record key.

MediaFoundation does not store or transmit the value itself because it provides only the interface. The concrete symbol-code implementation must document any persistence, logging, external processing, or temporary-file behavior.

Applications should avoid embedding secrets into QR codes or barcodes unless that exposure is explicitly intended and appropriately protected.

## No automatic persistence

MediaFoundation contains no database migration, repository, storage service, upload directory, cache backend, or state store for media content.

No raw media, media metadata, image edit specification, renderer path, or symbolic-code value is persisted merely because MediaFoundation is installed.

Persistence begins only when another component explicitly stores such data.

## No automatic network communication

The component contains no HTTP client and does not contact cloud media services, AI providers, CDNs, storage services, or remote processors by itself.

If a concrete implementation sends media or encoded values to another service, that transfer belongs to the implementation and must be assessed separately.

## No authentication or authorization

MediaFoundation does not establish user identity and does not make access decisions.

It does not provide:

- login handling
- sessions or cookies
- user or group lookup
- role checks
- per-media access control
- download authorization

The application exposing or processing media must enforce authorization at the appropriate boundary.

## No logging by the foundation

MediaFoundation does not use a logger and does not automatically record:

- media bytes
- MIME types
- media metadata
- file paths
- edit specifications
- watermark paths
- symbol-code values
- renderer errors

A concrete renderer or consumer may log these values. Logging policies should be reviewed separately because paths, media names, payload identifiers, or processing errors can contain personal or confidential information.

## Exceptions

`ImageRenderException` and `InvalidImageEditException` are generic exception types. MediaFoundation itself does not attach media content to these exceptions.

Concrete implementations should avoid placing raw media, secrets, or unnecessary file-system details into exception messages that may later be logged or displayed.

## Retention and deletion

MediaFoundation defines no persistent retention period because it owns no storage.

A deployment that persists media or edit specifications should define retention and deletion for at least:

- original media
- rendered derivatives
- temporary render files
- thumbnails and previews
- watermarks and watermark assets
- serialized `ImageEdit` specifications
- generated symbolic codes
- processing logs
- backups and replicas

Deletion of an in-memory `MediaFoundation` object does not delete copies created by storage, rendering, export, backup, or logging systems.

## Data minimization

Consumers should avoid placing unnecessary content into media objects or symbolic-code payloads. When generating derivatives, the active renderer should be configured to remove embedded metadata where appropriate and tested to ensure the requested behavior is actually applied.

Where only metadata is needed, applications should avoid retaining full media payloads longer than necessary.

## Responsibilities of implementations

Concrete implementations of MediaFoundation contracts should document and test, as applicable:

- supported media formats
- input validation and file-size limits
- malformed-media handling
- temporary-file behavior
- metadata extraction and stripping
- local or remote processing boundaries
- transport security
- authorization
- logging and error reporting
- storage encryption
- output-file cleanup
- retention and deletion
- backup behavior
- handling of untrusted SVG or other active formats

MediaFoundation provides the shared contracts and data structures. It does not replace these implementation-level privacy and security responsibilities.
