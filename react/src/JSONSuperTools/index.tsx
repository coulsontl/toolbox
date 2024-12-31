import React from 'react';
import LoadableWrapper from "../components/LoadableWrapper";
import { Card, Tabs, rem } from "@mantine/core"
import { IconTransformFilled, IconHome } from "@tabler/icons-react"
import { Route, Switch, useHistory } from "react-router";
import { Link } from "react-router-dom";
import JSONConversion from './JSONConversion'

export type JSONTypeItem = {
    name: string,
    id: string,
    icon: any,
    loadFN: any
}

interface JSONSuperToolsProps {
    heightOffset?: number;
}

export default ({ heightOffset = 0 }: JSONSuperToolsProps) => {
    const items: JSONTypeItem[] = [
        {
            name: 'JSON格式转换',
            id: 'convert',
            icon: IconTransformFilled,
            loadFN: () => Promise.resolve({ default: JSONConversion })
        },
    ]
    const activeId = items[0].id
    const hVal = `calc(100vh - ${heightOffset}px)`
    return (
        <Card p={0} withBorder style={{
            minHeight: hVal,
            height: hVal
        }}>
            <Tabs defaultValue={activeId} className="h-full flex flex-col">
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