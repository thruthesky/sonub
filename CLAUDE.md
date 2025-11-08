
# Workflow

- [ ] 모든 소스 코드의 주석과 모든 문서는 한국어로 작성해야 합니다. 반드시 utf-8 인코딩을 사용해야 합니다.
- [ ] 반드시 ./specs/instructions.md 및 ./specs/index.md 파일을 먼저 읽고 이해해야 합니다.
- [ ] 개발자 요청을 받으면 [Workflow for Claude SED Agent](#workflow-for-claude-sed-agent)을 따릅니다.
- [ ] 관련된 사양 문서를 모두 읽고 YAML 헤더를 구문 분석합니다.


## Workflow for Claude SED Agent

- [ ] Learn SED Methodology: Read the following pages and understand SED methodology:
  - https://sedai.dev
  - https://sedai.dev/principles
  - https://sedai.dev/philosophy

- [ ] Always Consult Specifications First: Before starting any development task, read ./specs/instructions.md and ./specs/index.md

- [ ] Strict Specification Obedience:
  - Follow specifications exactly, even if they appear incorrect
  - Never implement features not defined in specifications
  - Never modify specifications directly during implementation
  - If specifications are unclear, ask for clarification rather than guessing

- [ ] Error Reporting Protocol: When critical errors are detected in specifications:
  - Halt development immediately
  - Report the issue to developers with specific details
  - Recommend specification improvements
  - Wait for specification updates before continuing

- [ ] Ask for Clarification When Needed: When specifications are ambiguous or incomplete:
  - Request clarification from the developer instead of making assumptions
  - Ask for specific details about logic, source code, styles, or any other information needed
  - Never proceed with implementation based on guesswork or inference
  - Ensure all details are explicitly documented before continuing development
