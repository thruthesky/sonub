<div id="language-selector-app">
    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-globe"></i> <span>{{ currentLanguage }}</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            <li v-for="lang in languages" :key="lang.code">
                <a class="dropdown-item" href="#" @click.prevent="selectLanguage(lang.code)">
                    {{ lang.name }}
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    ready(() => {

        Vue.createApp({
            data() {
                return {
                    // 현재 언어 코드
                    currentLanguage: '<?php echo get_user_lang(); ?>',
                    // 지원하는 언어 목록
                    languages: [{
                            code: 'en',
                            name: 'English'
                        },
                        {
                            code: 'ko',
                            name: '한국어'
                        },
                        {
                            code: 'ja',
                            name: '日本語'
                        },
                        {
                            code: 'zh',
                            name: '中文'
                        }
                    ]
                };
            },
            methods: {
                /**
                 * 언어 선택 및 저장
                 * @param {string} languageCode - 선택된 언어 코드 (en, ko, ja, zh)
                 */
                async selectLanguage(languageCode) {
                    try {
                        // API 호출하여 언어 선택 저장
                        const response = await func('set_language', {
                            language_code: languageCode,
                            alertOnError: true
                        });

                        if (response.success) {
                            // 현재 언어 표시 업데이트
                            this.currentLanguage = languageCode;

                            // 페이지 새로고침하여 언어 변경 적용
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('언어 선택 중 오류 발생:', error);
                    }
                }
            }
        }).mount('#language-selector-app');
    });
</script>