import type { ReflectionSystemComponent } from '../../types/blueprint';

interface Props {
  chapter: ReflectionSystemComponent;
  onUpdate: (updates: Partial<ReflectionSystemComponent>) => void;
}

export default function FamilyEditor({ chapter, onUpdate }: Props) {
  void chapter;
  void onUpdate;
  return null;
}
