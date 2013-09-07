<?php
namespace Bilge\Stream;

trait VerbatimStream {}

stream_wrapper_register('verbatim', __NAMESPACE__ . '\Verbatim');