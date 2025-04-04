import React from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import { MantineProvider } from '@mantine/core'
import { Provider } from 'react-redux'
import { store } from './store/index'
import JSONSuperTools from './JSONSuperTools'
import './index-global.css'  // 导入全局样式
import { Toaster } from 'sonner'

// 获取根元素
const rootElement = document.getElementById('root')
if (!rootElement) throw new Error('Failed to find the root element')

// 创建根实例
const root = createRoot(rootElement)

// 使用新的 root.render 方法渲染应用
root.render(
    <React.StrictMode>
        <Provider store={store}>
            <BrowserRouter>
                <MantineProvider>
                    <Toaster position="top-center" richColors />
                    <JSONSuperTools heightOffset={0} />
                </MantineProvider>
            </BrowserRouter>
        </Provider>
    </React.StrictMode>
) 