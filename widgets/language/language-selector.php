<div id="language-selector-app">
    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-light rounded-pill border dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-translate text-primary"></i>
            <span class="text-dark">{{ getCurrentLanguageName() }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="languageDropdown">
            <li v-for="lang in languages" :key="lang.code">
                <a class="dropdown-item d-flex align-items-center"
                   href="#"
                   @click.prevent="selectLanguage(lang.code)"
                   :class="{ 'active': currentLanguage === lang.code }">
                    <i class="bi bi-check-circle-fill text-success me-2" v-if="currentLanguage === lang.code"></i>
                    <i class="bi bi-circle me-2 text-muted" v-else></i>
                    <span>{{ lang.name }}</span>
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
                 * 현재 언어의 전체 이름 가져오기
                 * @returns {string} 현재 언어의 이름
                 */
                getCurrentLanguageName() {
                    const lang = this.languages.find(l => l.code === this.currentLanguage);
                    return lang ? lang.name : this.currentLanguage;
                },
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