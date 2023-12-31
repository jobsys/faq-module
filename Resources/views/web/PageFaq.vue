<template>
	<NewbieTable ref="list" :url="route('api.manager.faq.items')" :columns="columns()" row-key="id">
		<template #prepend>
			<NewbieButton icon="UnorderedListOutlined" type="primary" @click="state.showGroupDrawer = true"> 常见问题分类管理 </NewbieButton>
		</template>

		<template #functional>
			<NewbieButton type="primary" icon="PlusOutlined" @click="onEdit(false)">新增常见问题 </NewbieButton>
		</template>
	</NewbieTable>

	<NewbieModal v-model:visible="state.showEditorModal" title="常见问题编辑" :width="1200">
		<NewbieEdit
			ref="edit"
			:fetch-url="state.url"
			:auto-load="!!state.url"
			:submit-url="route('api.manager.faq.edit')"
			:card-wrapper="false"
			full-width
			:form="getForm()"
			:form-config="{ labelCol: { span: 4 }, wrapperCol: { span: 19 } }"
			:on-close="closeEditorModal"
			@after-success="closeEditorModal(true)"
		/>
	</NewbieModal>

	<NewbieModal type="drawer" v-model:visible="state.showGroupDrawer" title="常见问题分类管理" :width="1000" @close="onCloseGroupGroupDrawer">
		<NewbieTable ref="groupList" :url="route('api.manager.faq.group.items')" :columns="groupColumns()" row-key="id">
			<template #functional>
				<NewbieButton type="primary" icon="PlusOutlined" @click="onEditGroup(false)">新增分类 </NewbieButton>
			</template>
		</NewbieTable>
	</NewbieModal>

	<NewbieModal v-model:visible="state.showGroupEditorModal" title="分类编辑">
		<NewbieEdit
			ref="edit"
			:submit-url="route('api.manager.faq.group.edit')"
			:card-wrapper="false"
			full-width
			:data="state.currentGroupItem"
			:form="getGroupForm()"
			:form-config="{ labelCol: { span: 4 }, wrapperCol: { span: 19 } }"
			:on-close="closeGroupEditorModal"
			@after-success="closeGroupEditorModal(true)"
		/>
	</NewbieModal>
</template>

<script setup>
import { h, inject, reactive, ref } from "vue"
import { NewbieTable, NewbieButton, NewbieEdit, NewbieModal, useTableActions } from "@web/components"
import { useFetch } from "@/js/hooks/web/network"
import { useModalConfirm } from "@/js/hooks/web/interact"
import { useProcessStatusSuccess } from "@/js/hooks/web/form"
import { message } from "ant-design-vue"
import { router } from "@inertiajs/vue3"

const route = inject("route")

const props = defineProps({
	groupOptions: {
		type: Array,
		default: () => [],
	},
})

const list = ref(null)
const groupList = ref(null)

const state = reactive({
	showGroupDrawer: false,
	showEditorModal: false,
	showGroupEditorModal: false,
	currentGroupItem: {},
	url: "",
})

const getForm = () => [
	{
		title: "问题标题",
		key: "title",
		required: true,
		style: {
			width: "500px",
		},
	},
	{
		title: "所属分类",
		key: "faq_group_id",
		type: "select",
		required: true,
		options: props.groupOptions,
	},
	{
		title: "内容",
		key: "content",
		type: "editor",
		required: true,
	},
	{
		title: "排序",
		key: "sort_order",
		type: "number",
	},
	{
		title: "显示状态",
		key: "is_active",
		type: "switch",
		defaultValue: true,
	},
]

const getGroupForm = () => [
	{
		title: "分类名称",
		key: "display_name",
		required: true,
	},
]

const onEdit = (item) => {
	state.url = item ? route("api.manager.faq.item", { id: item.id }) : ""
	state.showEditorModal = true
}

const onEditGroup = (item) => {
	if (item) {
		state.currentGroupItem = item
	} else {
		state.currentGroupItem = { display_name: "" }
	}

	state.showGroupEditorModal = true
}

const closeEditorModal = (isRefresh) => {
	if (isRefresh) {
		list.value.doFetch()
	}
	state.showEditorModal = false
}

const closeGroupEditorModal = (isRefresh) => {
	if (isRefresh) {
		groupList.value.doFetch()
	}
	state.showGroupEditorModal = false
}

const onCloseGroupGroupDrawer = () => {
	router.reload({ only: ["groupOptions"] })
}

const onDelete = (item) => {
	const modal = useModalConfirm(
		`您确认要删除 ${item.title} 吗？`,
		async () => {
			try {
				const res = await useFetch().post(route("api.manager.faq.delete"), { id: item.id })
				modal.destroy()
				useProcessStatusSuccess(res, () => {
					message.success("删除成功")
					list.value.doFetch()
				})
			} catch (e) {
				modal.destroy(e)
			}
		},
		true,
	)
}

const onDeleteGroup = (item) => {
	const modal = useModalConfirm(
		`您确认要删除 ${item.display_name} 吗？`,
		async () => {
			try {
				const res = await useFetch().post(route("api.manager.faq.group.delete"), { id: item.id })
				modal.destroy()
				useProcessStatusSuccess(res, () => {
					message.success("删除成功")
					groupList.value.doFetch()
				})
			} catch (e) {
				modal.destroy(e)
			}
		},
		true,
	)
}

const columns = () => [
	{
		title: "#",
		key: "index",
		width: 50,
		customRender: ({ index }) => h("span", {}, index + 1),
	},
	{
		title: "标题",
		width: 200,
		ellipsis: true,
		dataIndex: "title",
		nFilterType: "input",
	},
	{
		title: "所属分类",
		width: 200,
		dataIndex: ["group", "display_name"],
		nFilterKey: "faq_group_id",
		nFilterType: "select",
		nFilterOptions: props.groupOptions,
	},
	{
		title: "发布时间",
		width: 180,
		ellipsis: true,
		dataIndex: "created_at_datetime",
	},
	{
		title: "排序",
		width: 50,
		dataIndex: "sort_order",
	},
	{
		title: "显示状态",
		key: "is_active",
		width: 100,
		customRender: ({ record }) =>
			useTableActions({
				type: "a-tag",
				success: record.is_active,
				name: record.is_active ? "显示" : "隐藏",
			}),
	},
	{
		title: "操作",
		width: 180,
		fixed: "right",
		customRender: ({ record }) =>
			useTableActions([
				{
					name: "编辑",
					props: {
						icon: "EditOutlined",
						size: "small",
						auth: "api.manager.faq.edit",
					},
					action() {
						onEdit(record)
					},
				},
				{
					name: "删除",
					props: {
						icon: "DeleteOutlined",
						size: "small",
						auth: "api.manager.faq.delete",
					},
					action() {
						onDelete(record)
					},
				},
			]),
	},
]

const groupColumns = () => [
	{
		title: "分类名称",
		width: 200,
		dataIndex: "display_name",
		nFilterType: "input",
	},
	{
		title: "分类标识",
		dataIndex: "name",
		width: 100,
	},
	{
		title: "问题数",
		dataIndex: "faqs_count",
		width: 100,
	},
	{
		title: "创建时间",
		dataIndex: "created_at_datetime",
		width: 150,
	},
	{
		title: "操作",
		width: 180,
		fixed: "right",
		customRender: ({ record }) =>
			useTableActions([
				{
					name: "编辑",
					props: {
						icon: "EditOutlined",
						size: "small",
						auth: "api.manager.faq.group.edit",
					},
					action() {
						onEditGroup(record)
					},
				},
				{
					name: "删除",
					props: {
						icon: "DeleteOutlined",
						size: "small",
						auth: "api.manager.faq.group.delete",
					},
					action() {
						onDeleteGroup(record)
					},
				},
			]),
	},
]
</script>
