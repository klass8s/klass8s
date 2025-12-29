<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume;

enum HostPathVolumeType: string
{
    case None = '';
    case DirectoryOrCreate = 'DirectoryOrCreate';
    case Directory = 'Directory';
    case FileOrCreate = 'FileOrCreate';
    case File = 'File';
    case Socket = 'Socket';
    case CharDevice = 'CharDevice';
    case BlockDevice = 'BlockDevice';
}
