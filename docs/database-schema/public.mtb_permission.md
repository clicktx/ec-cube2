# public.mtb_permission

## Description

管理画面アクセス権限

## Columns

| Name | Type | Default | Nullable | Children | Parents | Comment |
| ---- | ---- | ------- | -------- | -------- | ------- | ------- |
| id | text |  | false |  |  | ID |
| name | text |  | true |  |  | 名称 |
| rank | smallint | 0 | false |  |  | 表示順 |

## Constraints

| Name | Type | Definition |
| ---- | ---- | ---------- |
| mtb_permission_pkey | PRIMARY KEY | PRIMARY KEY (id) |

## Indexes

| Name | Definition |
| ---- | ---------- |
| mtb_permission_pkey | CREATE UNIQUE INDEX mtb_permission_pkey ON public.mtb_permission USING btree (id) |

## Relations

![er](public.mtb_permission.svg)

---

> Generated by [tbls](https://github.com/k1LoW/tbls)