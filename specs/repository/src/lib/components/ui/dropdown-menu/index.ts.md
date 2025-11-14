---
name: index.ts
description: index UI 컴포넌트
version: 1.0.0
type: typescript
category: ui-component
original_path: src/lib/components/ui/dropdown-menu/index.ts
---

# index.ts

## 개요

**파일 경로**: `src/lib/components/ui/dropdown-menu/index.ts`
**파일 타입**: typescript
**카테고리**: ui-component

index UI 컴포넌트

## 소스 코드

```typescript
import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";
import CheckboxGroup from "./dropdown-menu-checkbox-group.svelte";
import CheckboxItem from "./dropdown-menu-checkbox-item.svelte";
import Content from "./dropdown-menu-content.svelte";
import Group from "./dropdown-menu-group.svelte";
import Item from "./dropdown-menu-item.svelte";
import Label from "./dropdown-menu-label.svelte";
import RadioGroup from "./dropdown-menu-radio-group.svelte";
import RadioItem from "./dropdown-menu-radio-item.svelte";
import Separator from "./dropdown-menu-separator.svelte";
import Shortcut from "./dropdown-menu-shortcut.svelte";
import Trigger from "./dropdown-menu-trigger.svelte";
import SubContent from "./dropdown-menu-sub-content.svelte";
import SubTrigger from "./dropdown-menu-sub-trigger.svelte";
import GroupHeading from "./dropdown-menu-group-heading.svelte";
const Sub = DropdownMenuPrimitive.Sub;
const Root = DropdownMenuPrimitive.Root;

export {
	CheckboxGroup,
	CheckboxItem,
	Content,
	Root as DropdownMenu,
	CheckboxGroup as DropdownMenuCheckboxGroup,
	CheckboxItem as DropdownMenuCheckboxItem,
	Content as DropdownMenuContent,
	Group as DropdownMenuGroup,
	Item as DropdownMenuItem,
	Label as DropdownMenuLabel,
	RadioGroup as DropdownMenuRadioGroup,
	RadioItem as DropdownMenuRadioItem,
	Separator as DropdownMenuSeparator,
	Shortcut as DropdownMenuShortcut,
	Sub as DropdownMenuSub,
	SubContent as DropdownMenuSubContent,
	SubTrigger as DropdownMenuSubTrigger,
	Trigger as DropdownMenuTrigger,
	GroupHeading as DropdownMenuGroupHeading,
	Group,
	GroupHeading,
	Item,
	Label,
	RadioGroup,
	RadioItem,
	Root,
	Separator,
	Shortcut,
	Sub,
	SubContent,
	SubTrigger,
	Trigger,
};

```

## 주요 기능

(이 섹션은 수동으로 업데이트 필요)

## 관련 파일

(이 섹션은 수동으로 업데이트 필요)
