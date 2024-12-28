
import React from 'react';
import GenCodeMirror from "../components/GenCodeMirror"
import exportUtils from "../utils/ExportUtils"
import { ActionIcon, Alert, Badge, Button, Card, Select, Title } from "@mantine/core"
import JSON5 from "json5";
import jsonmetautils from "./jsonmetautils";
import AlertUtils from "../utils/AlertUtils";
import _ from "lodash";
import { IconCopy, IconInfoCircle } from "@tabler/icons-react";
import { useClipboard } from "@mantine/hooks";
import FileExportUtils, { js_export_trigger } from "../utils/FileExportUtils";
import base64metautils from "./base64metautils";
import { useHistory } from "react-router";

export type JSONConversionState = {
    //
}
export type HistoryRecord = {
    label: string,
    value: string
}
export type JSONConversionNPState = {
    inputJSON: string,
    historicalRecords: HistoryRecord[]
}

const tryParseJSON = (text: string) => {
    try {
        const json = JSON5.parse(text)
        return json
    } catch (e) {
        return null
    }
}

const tryToFixJSON = (text: string) => {
    // 修复格式处理方法，按需添加
    const fixHandler = [
        (text: string) => `{${text}`,
        (text: string) => `${text}}`,
        (text: string) => `[${text}]`,
        (text: string) => `[${text.split('\n').filter(t => t && t.trim() !== '').join(',')}]`,
    ]

    let result = null
    for (let i = 0; i < fixHandler.length; i++) {
        result = tryParseJSON(fixHandler[i](text))
        if (result) {
            break
        }
    }

    return result
}

export default () => {
    const hist = useHistory()
    const rh = exportUtils.register('jsonsuper', {
        getPersistedStateFn: () => {
            return {
                anyFields: true,
            } satisfies JSONConversionState
        },
        getNotPersistedStateFn: () => {
            return {
                inputJSON: '',
                historicalRecords: [],
                errorReason: {
                    title: '',
                    content: ''
                }
            } satisfies JSONConversionState
        }
    })

    const clipboard = useClipboard({ timeout: 500 });

    if (!rh) {
        return ''
    }
    const jsonOperations: {
        name: string,
        desc?: string,
        icon?: any,
        onClick?: () => void
    }[] = [
            {
                name: '校验/格式化',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return
                    }

                    let fixed = false
                    let parsedJSON = tryParseJSON(rawVal)

                    // 如果解析失败，尝试修复
                    if (!parsedJSON) {
                        fixed = true
                        parsedJSON = tryToFixJSON(rawVal)
                    }

                    // 依然失败则报错
                    if (!parsedJSON) {
                        AlertUtils.alertErr('请检查 JSON 格式是否正确')
                        return
                    }

                    // 修复后成功则提示警告
                    if (fixed) {
                        AlertUtils.alertWarn('已自动修复格式问题')
                    }

                    const newval = jsonmetautils.beautify(`${JSON.stringify(parsedJSON)}`)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                    AlertUtils.alertSuccess('格式化成功，JSON格式校验通过')
                }
            },

            {
                name: '转义',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = jsonmetautils.escape(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },
            {
                name: '反转义',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = jsonmetautils.unescape(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },

            {
                name: '压缩',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = jsonmetautils.compress(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },
            {
                name: '编码Base64',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = base64metautils.encodeBase64(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },
            {
                name: '解码Base64',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = base64metautils.decodeBase64(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },
            {
                name: '中文转Unicode',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = jsonmetautils.chineseToUnicode(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },
            {
                name: 'Unicode转中文',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = jsonmetautils.unicodeToChinese(rawVal)
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },
            {
                name: '中文字符替换修复',
                onClick: () => {
                    const jState = rh.getActualValueInState()
                    const rawVal = jState.inputJSON
                    if (_.isEmpty(rawVal)) {
                        AlertUtils.alertErr('输入内容为空，请先提供JSON数据')
                        return;
                    }
                    const newval = jsonmetautils.noChinese(rawVal)
                    AlertUtils.alertSuccess('中文字符修复成功，已替换了若干中文字符')
                    rh.updateNonPState({
                        inputJSON: newval
                    })
                }
            },

        ]

    return <div className="w-full h-full flex flex-col sm:flex-row">
        <div className="w-full sm:h-full overflow-auto h-[500px] flex-1">
            <GenCodeMirror
                language="javascript"
                placeholder="请在此处输入JSON格式数据，在右侧面板轻击相关操作进行处理"
                onTextChange={(e) => {
                    rh.updateNonPState({
                        inputJSON: e
                    })
                }}
                directValue={rh.npState?.inputJSON} bigTextId={"thatisok"} />
        </div>
        <Card withBorder className="border-r-0 border-t-0 border-b-0 w-full sm:w-[300px]">
            <div className="mb-2">
                <div className="mb-1">
                    <Title order={6}>快速操作</Title>
                </div>
                {jsonOperations.map(x => {
                    return <Button className="mb-1 mr-1" size='xs' variant="outline" onClick={() => {
                        try {
                            rh.updateNonPState({
                                errorReason: {
                                    title: '',
                                    content: ''
                                }
                            })
                            x.onClick && x.onClick()
                        } catch (e: any) {
                            let errMsg = e.message || e
                            let errorTitle = '抱歉，捕获到一个错误'
                            // e.g. Unable to parse input as JSON. SyntaxError: JSON5: invalid character 'x' at 13:9
                            if (errMsg.includes('Unable to parse input as')) {
                                try {
                                    const errorBlock = errMsg.split('at')[1].trim().split(':')
                                    const line = errorBlock[0]
                                    const column = errorBlock[1]
                                    errorTitle = `解析错误于第${line}行`
                                    const inputJSONVal = rh.getActualValueInState().inputJSON
                                    const inputLines = inputJSONVal.split('\n')
                                    if (inputLines.length > line) {
                                        const thatLine = inputLines[line - 1]
                                        let noteStr: string[] = []
                                        for (let i = 0; i < column - 1; i++) {
                                            noteStr.push('-')
                                        }
                                        noteStr.push('^')
                                        errMsg = thatLine.replace(/\t/g, '-') + '\n' + noteStr.join("") // + '\n' + `${line}:${column}`
                                    }
                                } catch (e) {
                                    // ignore
                                    debugger;
                                }
                            } else {
                            }
                            rh.updateNonPState({
                                errorReason: {
                                    title: errorTitle,
                                    content: errMsg
                                }
                            })

                        }
                    }} key={x.name}>{x.name}</Button>
                })}
            </div>

            <div className="mt-4">
                <div className="">
                    <Title order={6}>更多操作</Title>
                </div>
                <div className="mt-2  " >
                    <Button size='xs' onClick={() => {
                        const str = `{
    "name": "工具箱",
  "count": 7800,
      "dates": [
      "2016-09","2018-06","2019-07","2020-01","2022-03","2024-05"
      ],
        "info": {
        "scope":"/",
        "noImplicitAny":true,
        "noLib":false,
        "extraLibs":[],
        "semanticValidation":true,
        "syntaxValidation":true,
        "codeGenTarget":"ES5",
        "moduleGenTarget":"",
      },
}`
                        rh.updateNonPState({
                            inputJSON: str
                        })
                    }} className="mr-1 mt-1">使用JSON示例</Button>
                    <Button className="mr-1 mt-1" size='xs' color='gray' onClick={() => {
                        rh.updateNonPState({
                            inputJSON: ''
                        })
                    }} variant="outline" >清空文本框</Button>
                    <Button className="mr-1 mt-1" size='xs' color='green' onClick={() => {
                        clipboard.copy(rh.npState?.inputJSON)
                        AlertUtils.alertSuccess('JSON数据已复制到剪贴板')
                    }}>复制结果</Button>
                    <Button className="mr-1 mt-1" size='xs' onClick={() => {
                        js_export_trigger({
                            saveValue: rh.npState?.inputJSON,
                            filename: `mdgjx.json`
                        })
                    }} color='violet'>导出结果</Button>
                </div>
                <div className="mt-2 space-x-1">
                </div>
            </div >

            <div className="mt-4">
                <div className="">
                    <Title order={6}>关于本工具</Title>
                </div>
                <div className="text-xs">
                    JSON超级工具涵盖JSON格式化、压缩、校验、转义、去转义、Base64编解码等实用功能，且兼容JSON5格式，可以帮助您快速处理JSON数据。
                </div>
            </div>
            <div className="mt-4">
                <div className="">
                    <Title order={6}>工具特色</Title>
                </div>
                <div className="space-x-2">
                    <Badge color="indigo" className="mt-2" >中文字符修复</Badge>
                    <Badge color="cyan" className="mt-2" >JSON5支持</Badge>
                </div>
            </div>
        </Card >
    </div >
}
