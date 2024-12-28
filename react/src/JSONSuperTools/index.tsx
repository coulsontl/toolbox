import React from 'react';
import LoadableWrapper from "../components/LoadableWrapper";
import { Card, Tabs, rem } from "@mantine/core"
import { IconTransformFilled } from "@tabler/icons-react"
import { Route, Switch, useHistory } from "react-router";
import { Link } from "react-router-dom";
import JSONConversion from './JSONConversion'

export type JSONTypeItem = {
    name: string,
    id: string,
    icon: any,
    loadFN: any
}
export default () => {
    const hist = useHistory()
    const iconStyle = { width: rem(12), height: rem(12) };
    const items: JSONTypeItem[] = [
        {
            name: 'JSON格式转换',
            id: 'convert',
            icon: IconTransformFilled,
            loadFN: () => Promise.resolve({ default: JSONConversion })
        },
        // {
        //     name: 'JSON格式提取',
        //     id: 'export',
        //     icon: IconOutlet,
        //     loadFN: () => import('./JSONExport.tsx')
        // },
        // {
        //     name: 'JSON差异对比',
        //     id: 'diff',
        //     icon: IconFileInfinity,
        //     loadFN: () => import('./JSONDiff.tsx')
        // },
    ]
    const activeId = items[0].id
    const hVal = 'calc(100vh - 0px)'
    return (
        <Card p={0} withBorder style={{
            minHeight: hVal,
            height: hVal
        }}>
            <Tabs defaultValue={activeId} className="h-full flex flex-col">
                <Tabs.List>
                    {
                        items.map(x => {
                            return (
                                <Tabs.Tab className="h-[35px]" value={x.id} leftSection={<x.icon style={iconStyle} />}>
                                    {x.name}
                                </Tabs.Tab>
                            )
                        })
                    }
                </Tabs.List>
                {
                    items.map(x => {
                        return (
                            <Tabs.Panel value={x.id} className="flex-1 overflow-auto">
                                <Switch>
                                    <LoadableWrapper id={`jsonsuper-${x.id}`} fn={x.loadFN} />
                                </Switch>
                            </Tabs.Panel>
                        )
                    })
                }
            </Tabs>
        </Card >
    )
}