import React from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import { MantineProvider } from '@mantine/core'
import { Provider } from 'react-redux'
import { store } from './store/index'
import JSONSuperTools from './JSONSuperTools'
import './index-global.css'
import { Toaster } from 'sonner'

interface MountOptions {
  heightOffset?: number;
}

function mount(el: HTMLElement, options: MountOptions = {}) {
  const root = createRoot(el)
  root.render(
    <React.StrictMode>
      <Provider store={store}>
        <BrowserRouter>
          <MantineProvider>
            <Toaster position="top-center" richColors />
            <JSONSuperTools heightOffset={options.heightOffset ?? 0} />
          </MantineProvider>
        </BrowserRouter>
      </Provider>
    </React.StrictMode>
  )
}

// 确保导出到全局
const JSONTools = {
  mount: mount
}

// 同时导出到全局和模块
if (typeof window !== 'undefined') {
  window.JSONTools = JSONTools;
}

export default JSONTools 