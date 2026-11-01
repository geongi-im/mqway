{{-- 첨부파일(PDF/PPT 등) 업로드 영역. create/edit 공용. 수정 화면에서는 $post 를 넘긴다. --}}
@php
    $attachMaxCount = \App\Http\Controllers\BoardInsightsController::ATTACH_MAX_COUNT;
    $attachMaxMb = \App\Http\Controllers\BoardInsightsController::ATTACH_MAX_KB / 1024;
    $attachExtensions = ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'hwp', 'hwpx'];
    $existingAttachments = isset($post) ? $post->getAttachments() : [];
    $attachErrors = array_unique(array_merge(
        $errors->get('mq_attach_file'),
        \Illuminate\Support\Arr::flatten($errors->get('mq_attach_file.*'))
    ));
@endphp

<div class="space-y-4 pt-6 border-t border-gray-100">
    <div class="flex justify-between items-end">
        <div>
            <label for="attachFileInput" class="text-sm font-semibold text-[#2D3047] block mb-1">첨부파일</label>
            <p class="text-xs text-gray-400">PDF, PPT, 워드, 엑셀, 한글 문서 / 최대 {{ $attachMaxCount }}개, 파일당 {{ $attachMaxMb }}MB 이하</p>
        </div>
        <label for="attachFileInput"
               class="cursor-pointer text-sm px-3 py-1.5 bg-[#9F5AFF]/10 text-[#7B2CBF] hover:bg-[#9F5AFF]/20 rounded-lg transition-colors font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            파일 선택
        </label>
        <input type="file"
               id="attachFileInput"
               name="mq_attach_file[]"
               multiple
               accept="{{ '.' . implode(',.', $attachExtensions) }}"
               class="hidden">
    </div>

    {{-- 기존 첨부파일 (수정 화면) --}}
    @if(count($existingAttachments) > 0)
        <ul id="existingAttachList" class="space-y-2">
            @foreach($existingAttachments as $attachment)
                <li class="flex items-center justify-between gap-3 h-12 px-4 border border-gray-200 rounded-xl bg-white">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="shrink-0 w-12 text-center text-[10px] font-bold uppercase rounded bg-gray-100 text-gray-600 py-1">{{ $attachment['ext'] }}</span>
                        <a href="{{ route('board-insights.download-file', [$post->idx, $attachment['file']]) }}"
                           class="text-sm text-gray-700 truncate hover:underline">{{ $attachment['name'] }}</a>
                        <span class="shrink-0 text-xs text-gray-400">{{ formatFileSize($attachment['size'], 1) }}</span>
                    </div>
                    <button type="button"
                            data-delete-url="{{ route('board-insights.delete-file', [$post->idx, $attachment['file']]) }}"
                            onclick="deleteExistingAttachment(this)"
                            class="shrink-0 text-xs px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors font-medium">
                        삭제
                    </button>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- 새로 고른 첨부파일 --}}
    <ul id="newAttachList" class="space-y-2"></ul>

    @foreach($attachErrors as $message)
        <p class="text-sm text-red-500">{{ $message }}</p>
    @endforeach
</div>

@push('scripts')
<script>
    (function() {
        const input = document.getElementById('attachFileInput');
        const list = document.getElementById('newAttachList');
        const maxCount = {{ $attachMaxCount }};
        const maxSize = {{ $attachMaxMb }} * 1024 * 1024;
        const allowedExtensions = @json($attachExtensions);
        let existingCount = {{ count($existingAttachments) }};
        let selectedFiles = [];

        function getExtension(name) {
            const dot = name.lastIndexOf('.');
            return dot === -1 ? '' : name.slice(dot + 1).toLowerCase();
        }

        function formatSize(bytes) {
            if (bytes < 1024 * 1024) {
                return (bytes / 1024).toFixed(1) + ' KB';
            }
            return (bytes / 1024 / 1024).toFixed(1) + ' MB';
        }

        // 고른 파일 목록을 input 에 다시 넣는다 (개별 삭제를 위해)
        function syncInput() {
            const transfer = new DataTransfer();
            selectedFiles.forEach(file => transfer.items.add(file));
            input.files = transfer.files;
        }

        function render() {
            list.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const item = document.createElement('li');
                item.className = 'flex items-center justify-between gap-3 h-12 px-4 border border-[#9F5AFF]/30 rounded-xl bg-[#9F5AFF]/5';

                const info = document.createElement('div');
                info.className = 'flex items-center gap-3 min-w-0';

                const badge = document.createElement('span');
                badge.className = 'shrink-0 w-12 text-center text-[10px] font-bold uppercase rounded bg-[#9F5AFF]/10 text-[#7B2CBF] py-1';
                badge.textContent = getExtension(file.name);

                // 파일명은 반드시 textContent 로 넣는다
                const name = document.createElement('span');
                name.className = 'text-sm text-gray-700 truncate';
                name.textContent = file.name;

                const size = document.createElement('span');
                size.className = 'shrink-0 text-xs text-gray-400';
                size.textContent = formatSize(file.size);

                info.append(badge, name, size);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'shrink-0 text-xs px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors font-medium';
                removeButton.textContent = '빼기';
                removeButton.addEventListener('click', function() {
                    selectedFiles.splice(index, 1);
                    syncInput();
                    render();
                });

                item.append(info, removeButton);
                list.appendChild(item);
            });
        }

        input.addEventListener('change', function() {
            const picked = Array.from(input.files);

            for (const file of picked) {
                if (existingCount + selectedFiles.length >= maxCount) {
                    alert('첨부파일은 게시글당 최대 ' + maxCount + '개까지 올릴 수 있습니다.');
                    break;
                }
                if (!allowedExtensions.includes(getExtension(file.name))) {
                    alert('허용되지 않는 파일 형식입니다: ' + file.name);
                    continue;
                }
                if (file.size > maxSize) {
                    alert('파일당 {{ $attachMaxMb }}MB 이하만 올릴 수 있습니다: ' + file.name);
                    continue;
                }
                selectedFiles.push(file);
            }

            syncInput();
            render();
        });

        // 기존 첨부파일 삭제 (수정 화면)
        window.deleteExistingAttachment = function(button) {
            if (!confirm('첨부파일을 삭제하시겠습니까?\n삭제된 파일은 복구할 수 없습니다.')) {
                return;
            }

            fetch(button.dataset.deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.closest('li').remove();
                    existingCount--;
                } else {
                    alert(data.message || '첨부파일 삭제 중 오류가 발생했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('첨부파일 삭제 중 오류가 발생했습니다.');
            });
        };
    })();
</script>
@endpush
