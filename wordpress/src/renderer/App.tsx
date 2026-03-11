import { useRef } from 'react';
import type { Blueprint } from '../../types/blueprint';
import { useStyleContract } from './hooks/useStyleContract';
import StandardMode from './components/StandardMode';
import ImmersiveMode from './components/ImmersiveMode';

interface Props { blueprint: Blueprint; postId: number }

export default function App({ blueprint, postId }: Props) {
  const rootRef = useRef<HTMLDivElement>(null);
  useStyleContract(rootRef, blueprint.appearance ?? {});

  return (
    <div ref={rootRef} className="reci-renderer-root">
      {blueprint.mode === 'immersive' && blueprint.chapters ? (
        <ImmersiveMode chapters={blueprint.chapters} postId={postId} />
      ) : (
        <StandardMode scenes={blueprint.scenes ?? []} />
      )}
    </div>
  );
}
